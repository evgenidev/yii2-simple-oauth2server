<?php

declare(strict_types=1);

namespace EvgeniDev\Yii2\OAuth2Server\Controllers;

use EvgeniDev\Yii2\OAuth2Server\Components\Controller;
use EvgeniDev\Yii2\OAuth2Server\Exceptions\ValidationException;
use EvgeniDev\Yii2\OAuth2Server\Forms\OAuthAuthorizeForm;
use EvgeniDev\Yii2\OAuth2Server\Module;
use EvgeniDev\Yii2\OAuth2Server\Records\OAuthApproval;
use EvgeniDev\Yii2\OAuth2Server\Services\OAuthApprovalCreateService;
use EvgeniDev\Yii2\OAuth2Server\Services\OAuthCodeCreateService;
use EvgeniDev\Yii2\OAuth2Server\Services\OAuthRequestService;
use Exception;
use Yii;
use yii\web\Response;

/**
 * Authorization controller.
 */
final class AuthorizeController extends Controller
{
    /**
     * Client Authorization.
     */
    public function actionIndex()
    {
        $request = Yii::$app->getRequest();
        $webUser = Yii::$app->getUser();

        $client = (new OAuthRequestService())->checkClient($request);

        if ($client instanceof Response) {
            return $client;
        }

        if ($webUser->getIsGuest()) {
            $redirectParam = false === strripos($webUser->loginUrl, '?') ? '?redirectUrl' : '&redirectUrl';
            return $this->redirect([$webUser->loginUrl.$redirectParam.'='.urlencode($request->getAbsoluteUrl())]);
        }

        $approval = OAuthApproval::find()
            ->byClientID($request->get('client_id'))
            ->byUserID($webUser->getId())
            ->one();

        $redirectUrl = $request->get('redirect_uri');

        if ($approval === null) {
            $form = new OAuthAuthorizeForm();

            if ($request->getIsPost()) {
                if ($request->post('deny')) {
                    return $this->redirect($redirectUrl.'?'.http_build_query(['error' => 'Access deny']));
                }

                $form->setAttributes($request->post('OAuthAuthorizeForm'));

                if ($form->validate() === false) {
                    throw new ValidationException($form);
                }

                try {
                    (new OAuthApprovalCreateService())($form, $webUser);
                } catch (Exception $e) {
                    Yii::$app->getSession()->addFlash('danger', Module::t('app', 'oauth.approval.error'));

                    return $this->redirect(['/']);
                }
            } else {
                return $this->render(Yii::$app->getModule('oauth2')->authorizeView ?? 'index', [
                    'client' => $client,
                    'user' => $webUser->getIdentity(),
                    'form' => $form,
                    'state' => $request->get('state'),
                ]);
            }
        }

        try {
            $code = (new OAuthCodeCreateService())($client, $webUser);
        } catch (Exception $e) {
            Yii::$app->getSession()->addFlash('danger', Module::t('app', 'oauth.error'));

            return $this->redirect(['/']);
        }

        $redirectUrl .= '?code='.$code->getAuthorizationCode();

        if ($request->get('state')) {
            $redirectUrl .= '&state='.$request->get('state');
        }

        return $this->redirect($redirectUrl);
    }
}
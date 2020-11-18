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
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\filters\RateLimiter;
use yii\web\Response;

/**
 * Authorization controller.
 */
final class AuthorizeController extends Controller
{
    /**
     * {@inheritDoc}
     */
    public function behaviors(): array
    {
        if (false === $this->module->spaApp) {
            return parent::behaviors();
        }

        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'rateLimiter' => [
                'class' => RateLimiter::class,
            ],
            'authenticator' => [
                'except' => ['index'],
                'class' => CompositeAuth::class,
                'authMethods' => [
                    HttpBearerAuth::class,
                ],
            ],
        ];
    }

    /**
     * Client Authorization.
     */
    public function actionIndex(): Response
    {
        $request = Yii::$app->getRequest();
        $webUser = Yii::$app->getUser();
        $session = Yii::$app->getSession();

        $client = (new OAuthRequestService())->checkClient($request);

        if ($client instanceof Response) {
            return $client;
        }

        if ($webUser->getIsGuest()) {
            if (false === $this->module->spaApp) {
                $redirectParam = false === strripos($webUser->loginUrl, '?') ? '?' : '&';
                return $this->redirect([$webUser->loginUrl.$redirectParam.'redirectUrl='.urlencode($request->getAbsoluteUrl())]);
            }

            return $this->response([
                'error' => 'Unauthorized',
                'redirectUrl' => $request->getAbsoluteUrl(),
            ], 401);
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

                $form->setAttributes($request->post('OAuthAuthorizeForm') ?? $request->post());

                if (false === $form->validate()) {
                    throw new ValidationException($form);
                }

                try {
                    (new OAuthApprovalCreateService())($form, $webUser);
                } catch (Exception $e) {
                    if (false === $this->module->spaApp) {
                        $session->addFlash('danger', Module::t('app', 'oauth.approval.error'));

                        return $this->redirect(['/']);
                    }

                    return $this->response([
                        'error' => Module::t('app', 'oauth.approval.error'),
                    ], 500);
                }
            } else {
                if (false === $this->module->spaApp) {
                    return $this->render($this->module->authorizeView ?? 'index', [
                        'client' => $client,
                        'user' => $webUser->getIdentity(),
                        'form' => $form,
                        'state' => $request->get('state'),
                    ]);
                }
                
                return $this->response([
                    'clientName' => $client->getName(),
                    'state' => $request->get('state'),
                ], 201);
            }
        }

        try {
            $code = (new OAuthCodeCreateService())($client, $webUser);
        } catch (Exception $e) {
            if (false === $this->module->spaApp) {
                $session->addFlash('danger', Module::t('app', 'oauth.error'));

                return $this->redirect(['/']);
            }

            return $this->response([
                'error' => Module::t('app', 'oauth.approval.error'),
            ], 500);
        }

        $redirectUrl .= '?code='.$code->getAuthorizationCode();

        if ($request->get('state')) {
            $redirectUrl .= '&state='.$request->get('state');
        }

        return $this->redirect($redirectUrl);
    }
}

<?php

declare(strict_types=1);

namespace EvgeniDev\Yii2\OAuth2Server\Controllers;

use EvgeniDev\Yii2\OAuth2Server\Components\Controller;
use EvgeniDev\Yii2\OAuth2Server\Services\OAuthAccessTokenCreateService;
use EvgeniDev\Yii2\OAuth2Server\Services\OAuthRequestService;
use Exception;
use Yii;
use yii\web\Response;

/**
 * Access token controller.
 */
final class AccessTokenController extends Controller
{
    /**
     * Generates the Access Token.
     */
    public function actionIndex()
    {
        $request = Yii::$app->getRequest();

        $authCode = (new OAuthRequestService())->checkCode($request);

        if ($authCode instanceof Response) {
            return $authCode;
        }

        $approval = (new OAuthRequestService())->checkClientApproval($request, $authCode);

        if ($approval instanceof Response) {
            return $approval;
        }

        try {
            $token = (new OAuthAccessTokenCreateService())($approval);
        } catch (Exception $e) {
            return $this->asJson([
                'error' => 'Invalid Condition',
                'error_description' => 'Invalid Condition.',
            ], 500);
        }

        $authCode->delete();

        return $this->asJson([
            'access_token' => $token->getAccessToken(),
            'expires_at' => $token->getExpiresAt(),
        ]);
    }
}
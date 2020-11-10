<?php

declare(strict_types=1);

namespace EvgeniDev\Yii2\OAuth2Server\Services;

use EvgeniDev\Yii2\OAuth2Server\Records\OAuthApproval;
use EvgeniDev\Yii2\OAuth2Server\Records\OAuthAuthorizationCode;
use EvgeniDev\Yii2\OAuth2Server\Records\OAuthClient;
use Yii;
use yii\web\Request;
use yii\web\Response;

/**
 * Oauth request check service.
 */
final class OAuthRequestService
{
    /**
     * Checks if client exist.
     */
    public function checkClient(Request $request)
    {
        if (empty($request->get('response_type')) || empty($request->get('client_id')) || empty($request->get('redirect_uri'))) {
            return $this->asJson([
                'error' => 'Invalid Request',
                'error_description' => 'The request is missing a required parameter. Check: response_type, client_id, redirect_uri',
            ]);
        }

        $client = OAuthClient::find()
            ->byClientID($request->get('client_id'))
            ->byRedirectUri($request->get('redirect_uri'))
            ->one();

        if ($client === null) {
            return $this->asJson([
                'error' => 'Invalid Client',
                'error_description' => 'Client Authentication failed.',
            ]);
        }

        return $client;
    }

    /**
     * Checks if code is valid.
     */
    public function checkCode(Request $request)
    {
        if (empty($request->post('grant_type')) || empty($request->post('client_id')) || empty($request->post('client_secret')) || empty($request->post('redirect_uri')) || empty($request->post('code'))) {
            return $this->asJson([
                'error' => 'Invalid Request',
                'error_description' => 'The request is missing a required parameter. Check: grant_type, client_id, client_secret, redirect_uri, code.',
            ]);
        }

        $authCode = OAuthAuthorizationCode::find()
            ->byAuthorizationCode($request->post('code'))
            ->byClientID($request->post('client_id'))
            ->one();

        if ($authCode === null) {
            return $this->asJson([
                'error' => 'Invalid Client',
                'error_description' => 'Invalid Code or Client.',
            ]);
        }

        return $authCode;
    }

    /**
     * Checks if client exists and has approval.
     */
    public function checkClientApproval(Request $request, OAuthAuthorizationCode $authCode)
    {
        $client = OAuthClient::find()
            ->byClientID($request->post('client_id'))
            ->byClientSecret($request->post('client_secret'))
            ->byRedirectUri($request->post('redirect_uri'))
            ->one();

        $approval = OAuthApproval::find()
            ->byClientID($request->post('client_id'))
            ->byUserID($authCode->getUserID())
            ->one();

        if ($client === null || $approval === null) {
            $authCode->delete();

            return $this->asJson([
                'error' => 'Invalid Client',
                'error_description' => 'Invalid Client.',
            ]);
        }

        return $approval;
    }

    /**
     * Sends data formatted as JSON.
     */
    private function asJson(array $data): Response
    {
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_XML;
        $response->data = $data;

        return $response;
    }
}
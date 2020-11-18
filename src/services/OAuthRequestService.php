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
            return $this->response([
                'error' => 'Invalid Request',
                'error_description' => 'The request is missing a required parameter. Check: response_type, client_id, redirect_uri',
            ], 500);
        }

        $client = OAuthClient::find()
            ->byClientID($request->get('client_id'))
            ->byRedirectUri($request->get('redirect_uri'))
            ->one();

        if ($client === null) {
            return $this->response([
                'error' => 'Invalid Client',
                'error_description' => 'Client Authentication failed.',
            ], 500);
        }

        return $client;
    }

    /**
     * Checks if code is valid.
     */
    public function checkCode(Request $request)
    {
        if (empty($request->post('grant_type')) || empty($request->post('client_id')) || empty($request->post('client_secret')) || empty($request->post('redirect_uri')) || empty($request->post('code'))) {
            return $this->response([
                'error' => 'Invalid Request',
                'error_description' => 'The request is missing a required parameter. Check: grant_type, client_id, client_secret, redirect_uri, code.',
            ], 500);
        }

        $authCode = OAuthAuthorizationCode::find()
            ->byAuthorizationCode($request->post('code'))
            ->byClientID($request->post('client_id'))
            ->one();

        if ($authCode === null) {
            return $this->response([
                'error' => 'Invalid Client',
                'error_description' => 'Invalid Code or Client.',
            ], 500);
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

            return $this->response([
                'error' => 'Invalid Client',
                'error_description' => 'Invalid Client.',
            ], 500);
        }

        return $approval;
    }

    /**
     * Format data.
     */
    public function response(array $data, int $httpStatusCode = 200): Response
    {
        $response = Yii::$app->getResponse();
        $response->format = Yii::$app->getModule('oauth2')->responseFormat;
        $response->data = $data;
        $response->statusCode = $httpStatusCode;

        return $response;
    }
}
<?php

declare(strict_types=1);

namespace EvgeniDev\Yii2\OAuth2Server\Commands;

use EvgeniDev\Yii2\OAuth2Server\Exceptions\InvalidConditionException;
use EvgeniDev\Yii2\OAuth2Server\Records\OAuthClient;

/**
 * OAuth console controller.
 */
final class DefaultController extends \yii\console\Controller
{
    /**
     * Creates Oauth client.
     *
     * @throws \EvgeniDev\Yii2\OAuth2Server\Exceptions\InvalidConditionException
     */
    public function actionCreateClient(string $redirectUri, string $name)
    {
        $client = (new OAuthClient())
            ->setName($name)
            ->setRedirectUri($redirectUri)
            ->generateClientID()
            ->generateClientSecret()
            ->setGrantTypes(OAuthClient::GRANT_TYPE_CODE);

        if (false === $client->save()) {
            throw new InvalidConditionException();
        }

        $client->refresh();

        echo "\n".'clientID: '.$client->getClientID()."\n";
        echo 'clientSecret: '.$client->getClientSecret()."\n\n";
    }

    /**
     * Deletes Oauth client.
     *
     * @throws \EvgeniDev\Yii2\OAuth2Server\Exceptions\InvalidConditionException
     */
    public function actionDeleteClient(string $name, string $redirectUri = null)
    {
        $client = OAuthClient::find()
            ->byName($name)
            ->byRedirectUri($redirectUri)
            ->one();

        if (false === $client->delete()) {
            throw new InvalidConditionException();
        }

        echo "\n".'Client: '.$name.'is Deleted'."\n";
    }
}

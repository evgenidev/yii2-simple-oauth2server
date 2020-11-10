<?php

declare(strict_types=1);

namespace EvgeniDev\Yii2\OAuth2Server\Services;

use EvgeniDev\Yii2\OAuth2Server\Exceptions\InvalidConditionException;
use EvgeniDev\Yii2\OAuth2Server\Exceptions\ValidationException;
use EvgeniDev\Yii2\OAuth2Server\Records\OAuthAuthorizationCode;
use EvgeniDev\Yii2\OAuth2Server\Records\OAuthClient;
use yii\web\User;
use Throwable;

/**
 * Authorisation Code generate service.
 */
final class OAuthCodeCreateService
{
    /**
     * Generates and installs Authorisation Code.
     *
     * @throws \EvgeniDev\Yii2\OAuth2Server\Exceptions\ValidationException
     * @throws \EvgeniDev\Yii2\OAuth2Server\Exceptions\InvalidConditionException
     * @throws \Throwable
     */
    public function __invoke(OAuthClient $client, User $webuser): OAuthAuthorizationCode
    {
        try {
            $code = OAuthAuthorizationCode::find()
                ->byClientID($client->getClientID())
                ->byUserID($webuser->getId())
                ->one();

            if ($code === null) {
                $code = (new OAuthAuthorizationCode())
                    ->setClientID($client->getClientID())
                    ->setRedirectUri($client->getRedirectUri())
                    ->setUserID($webuser->getId());
            }

            $code->generateAuthorizationCode();

            if (false === $code->validate()) {
                throw new ValidationException($code);
            }

            if (false === $code->save()) {
                throw new InvalidConditionException();
            }

            $code->refresh();

            return $code;
        } catch (Throwable $e) {
            throw $e;
        }
    }
}

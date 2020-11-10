<?php

declare(strict_types=1);

namespace EvgeniDev\Yii2\OAuth2Server\Services;

use EvgeniDev\Yii2\OAuth2Server\Exceptions\InvalidConditionException;
use EvgeniDev\Yii2\OAuth2Server\Exceptions\ValidationException;
use EvgeniDev\Yii2\OAuth2Server\Forms\OAuthAuthorizeForm;
use EvgeniDev\Yii2\OAuth2Server\Records\OAuthApproval;
use Throwable;
use yii\web\User;

/**
 * Oauth authorisation service.
 */
final class OAuthApprovalCreateService
{
    /**
     * Creates data transfer approval.
     *
     * @throws \Throwable
     * @throws \EvgeniDev\Yii2\OAuth2Server\Exceptions\InvalidConditionException
     * @throws \EvgeniDev\Yii2\OAuth2Server\Exceptions\ValidationException
     */
    public function __invoke(OAuthAuthorizeForm $form, User $webuser): OAuthApproval
    {
        try {
            $approval = (new OAuthApproval())
                ->setClientID($form->clientID)
                ->setUserID($webuser->getId());

            if (false === $approval->validate()) {
                throw new ValidationException($approval);
            }

            if (false === $approval->save()) {
                throw new InvalidConditionException();
            }

            return $approval;
        } catch (Throwable $e) {
            throw $e;
        }
    }
}

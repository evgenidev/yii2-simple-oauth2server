<?php

declare(strict_types=1);

namespace EvgeniDev\Yii2\OAuth2Server\Records;

use EvgeniDev\Yii2\OAuth2Server\Components\ActiveRecord;
use EvgeniDev\Yii2\OAuth2Server\Queries\OAuthApprovalQuery;
use Yii;

/**
 * OauthApproval AR.
 */
class OAuthApproval extends ActiveRecord
{
    /**
     * {@inheritDoc}
     */
    public static function find(): OAuthApprovalQuery
    {
        return new OAuthApprovalQuery(self::class);
    }

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        $userClassString = Yii::$app->getModule('oauth2')->identityClass;
        $userClass = new $userClassString();

        return [
            [['clientID', 'userID'], 'required'],
            [['clientID'], 'exist', 'targetClass' => OAuthClient::class, 'targetAttribute' => ['clientID' => 'clientID']],
            [['clientID', 'userID'], 'unique', 'targetAttribute' => ['clientID', 'userID']],
            [['userID'], 'exist', 'targetClass' => $userClassString, 'targetAttribute' => ['userID' => $userClass::primaryKey()[0]]],
        ];
    }

    public function getClientID(): string
    {
        return $this->getAttribute('clientID');
    }

    public function setClientID(string $clientID): self
    {
        $this->setAttribute('clientID', $clientID);

        return $this;
    }

    public function getUserID(): string
    {
        return $this->getAttribute('userID');
    }

    public function setUserID($userID): self
    {
        $this->setAttribute('userID', $userID);

        return $this;
    }
}

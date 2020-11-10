<?php

declare(strict_types=1);

namespace EvgeniDev\Yii2\OAuth2Server\Records;

use EvgeniDev\Yii2\OAuth2Server\Components\ActiveRecord;
use EvgeniDev\Yii2\OAuth2Server\Queries\OAuthAccessTokenQuery;
use Yii;
use DateTime;

/**
 * OAuthAccessToken AR.
 */
class OAuthAccessToken extends ActiveRecord
{
    /**
     * {@inheritDoc}
     */
    public static function find(): OAuthAccessTokenQuery
    {
        return new OAuthAccessTokenQuery(self::class);
    }

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        $userClassString = Yii::$app->getModule('oauth2')->identityClass;
        $userClass = new $userClassString();

        return [
            [['accessToken', 'clientID', 'userID', 'expiresAt'], 'required'],
            [['accessToken'], 'string', 'max' => 48],
            [['clientID'], 'exist', 'targetClass' => OAuthClient::class, 'targetAttribute' => ['clientID' => 'clientID']],
            [['userID'], 'exist', 'targetClass' => $userClassString, 'targetAttribute' => ['userID' => $userClass::primaryKey()[0]]],
        ];
    }

    public function getAccessToken(): string
    {
        return $this->getAttribute('accessToken');
    }

    public function generateAccessToken(): self
    {
        $token = Yii::$app
            ->getSecurity()
            ->generateRandomString(48);

        $this->setAttribute('accessToken', $token);

        return $this;
    }

    public function getUserID(): string
    {
        return $this->getAttribute('userID');
    }

    public function setUserID($userID = null): self
    {
        $this->setAttribute('userID', $userID);

        return $this;
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

    public function getExpiresAt(): string
    {
        return $this->getAttribute('expiresAt');
    }

    public function setExpiresAt($expiresAt = null): self
    {
        if ($expiresAt === null) {
            $datetime = new DateTime();
            $datetime->modify('+'.Yii::$app->getModule('oauth2')->accessTokenLifetime.' seconds');
            $expiresAt = $datetime->format('Y-m-d H:i:s');
        }

        $this->setAttribute('expiresAt', $expiresAt);

        return $this;
    }
}

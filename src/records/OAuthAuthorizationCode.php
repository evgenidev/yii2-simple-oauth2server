<?php

declare(strict_types=1);

namespace EvgeniDev\Yii2\OAuth2Server\Records;

use EvgeniDev\Yii2\OAuth2Server\Queries\OAuthAuthorizationCodeQuery;
use Yii;
use yii\db\ActiveRecord;

/**
 * OAuthAuthorizationCode AR.
 */
class OAuthAuthorizationCode extends ActiveRecord
{
    /**
     * {@inheritDoc}
     */
    public static function tableName(): string
    {
        return 'OAuthAuthorizationCode';
    }

    /**
     * {@inheritDoc}
     */
    public static function find(): OAuthAuthorizationCodeQuery
    {
        return new OAuthAuthorizationCodeQuery(self::class);
    }

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        $userClassString = Yii::$app->getModule('oauth2')->identityClass;
        $userClass = new $userClassString();

        return [
            [['clientID', 'userID', 'redirectUri'], 'required'],
            [['authorizationCode'], 'string'],
            [['redirectUri'], 'string', 'max' => 1000],
            [['clientID'], 'exist', 'targetClass' => OAuthClient::class, 'targetAttribute' => ['clientID' => 'clientID']],
            [['userID'], 'exist', 'targetClass' => $userClassString, 'targetAttribute' => ['userID' => $userClass::primaryKey()[0]]],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function deleteAll($condition = null, $params = []): int
    {
        $command = ActiveRecord::getDb()->createCommand();
        $command->delete(static::tableName(), $condition, $params);

        return $command->execute();
    }

    public function getAuthorizationCode(): string
    {
        return $this->getAttribute('authorizationCode');
    }

    public function generateAuthorizationCode(): self
    {
        $code = Yii::$app
            ->getSecurity()
            ->generateRandomString(48);

        $this->setAttribute('authorizationCode', $code);

        return $this;
    }

    public function getUserID(): string
    {
        return $this->getAttribute('userID');
    }

    public function setUserID(string $userID): self
    {
        $this->setAttribute('userID', $userID);

        return $this;
    }

    public function getRedirectUri(): string
    {
        return $this->getAttribute('redirectUri');
    }

    public function setRedirectUri(string $redirectUri): self
    {
        $this->setAttribute('redirectUri', $redirectUri);

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

    public function getClientSecret(): string
    {
        return $this->getAttribute('clientSecret');
    }
}

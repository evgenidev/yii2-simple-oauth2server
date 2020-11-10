<?php

declare(strict_types=1);

namespace EvgeniDev\Yii2\OAuth2Server\Records;

use EvgeniDev\Yii2\OAuth2Server\Components\ActiveRecord;
use EvgeniDev\Yii2\OAuth2Server\Queries\OAuthClientQuery;
use Yii;

/**
 * OAuthClient AR.
 */
class OAuthClient extends ActiveRecord
{
    const GRANT_TYPE_CODE = 'authorization_code';

    /**
     * {@inheritDoc}
     */
    public static function find(): OAuthClientQuery
    {
        return new OAuthClientQuery(self::class);
    }

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        $userClassString = Yii::$app->getModule('oauth2')->identityClass;
        $userClass = new $userClassString();

        return [
            [['name', 'clientID', 'clientSecret', 'redirectUri', 'grantTypes'], 'required'],
            [['name', 'redirectUri', 'grantTypes'], 'string', 'max' => '255'],
            [['clientID', 'clientSecret'], 'string', 'max' => 48],
            [['redirectUri'], 'string', 'max' => 1000],
            [['name', 'redirectUri'], 'unique', 'targetAttribute' => ['name', 'redirectUri']],
            [['userID'], 'exist', 'targetClass' => $userClassString, 'targetAttribute' => ['userID' => $userClass::primaryKey()[0]]],
        ];
    }

    public function getName(): string
    {
        return $this->getAttribute('name');
    }

    public function setName(string $name): self
    {
        $this->setAttribute('name', $name);

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

    public function generateClientID(): self
    {
        $clientID = Yii::$app
            ->getSecurity()
            ->generateRandomString(48);

        $this->setClientID($clientID);

        return $this;
    }

    public function setClientID(string $clientID): self
    {
        $this->setAttribute('clientID', $clientID);

        return $this;
    }

    public function generateClientSecret(): self
    {
        $clientSecret = Yii::$app
            ->getSecurity()
            ->generateRandomString(48);

        $this->setClientSecret($clientSecret);

        return $this;
    }

    public function setClientSecret(string $clientSecret)
    {
        $this->setAttribute('clientSecret', $clientSecret);

        return $this;
    }

    public function getClientSecret(): string
    {
        return $this->getAttribute('clientSecret');
    }

    public function getGrantTypes(): string
    {
        return $this->getAttribute('grantTypes');
    }

    public function setGrantTypes(string $grantTypes): self
    {
        $this->setAttribute('grantTypes', $grantTypes);

        return $this;
    }
}

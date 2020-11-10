<?php

declare(strict_types=1);

namespace EvgeniDev\Yii2\OAuth2Server\Queries;

use EvgeniDev\Yii2\OAuth2Server\Components\ActiveQuery;

/**
 * OAuthClient repository.
 *
 * @method \EvgeniDev\Yii2\OAuth2Server\Records\OAuthClient one($db = null)
 * @method \EvgeniDev\Yii2\OAuth2Server\Records\OAuthClient[] all($db = null)
 */
class OAuthClientQuery extends ActiveQuery
{
    public function byID(string $id): self
    {
        return $this->andWhere(['ID' => $id]);
    }

    public function byName(string $name): self
    {
        return $this->andWhere(['name' => $name]);
    }

    public function byClientID(string $clientID): self
    {
        return $this->andWhere(['clientID' => $clientID]);
    }

    public function byClientSecret(string $clientSecret): self
    {
        return $this->andWhere(['clientSecret' => $clientSecret]);
    }

    public function byUserID(string $userID): self
    {
        return $this->andWhere(['userID' => $userID]);
    }

    public function byRedirectUri(string $redirectUri): self
    {
        return $this->andWhere(['redirectUri' => $redirectUri]);
    }
}

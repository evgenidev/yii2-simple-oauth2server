<?php

declare(strict_types=1);

namespace EvgeniDev\Yii2\OAuth2Server\Queries;

use EvgeniDev\Yii2\OAuth2Server\Components\ActiveQuery;

/**
 * OAuthAccessToken repository.
 *
 * @method \EvgeniDev\Yii2\OAuth2Server\Records\OAuthAccessToken one($db = null)
 * @method \EvgeniDev\Yii2\OAuth2Server\Records\OAuthAccessToken[] all($db = null)
 */
class OAuthAccessTokenQuery extends ActiveQuery
{
    public function byID(string $id): self
    {
        return $this->andWhere(['ID' => $id]);
    }

    public function byAccessToken(string $accessToken): self
    {
        return $this->andWhere(['accessToken' => $accessToken]);
    }

    public function byClientID(string $clientID): self
    {
        return $this->andWhere(['clientID' => $clientID]);
    }

    public function byUserID(string $userID): self
    {
        return $this->andWhere(['userID' => $userID]);
    }
}

<?php

declare(strict_types=1);

namespace EvgeniDev\Yii2\OAuth2Server\Components;

/**
 * Extended ActiveQuery.
 *
 * {@inheritDoc}
 */
class ActiveQuery extends \yii\db\ActiveQuery
{
    /**
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();

        $this->andWhere('"deletedAt" IS NULL');
    }

    public function orderByCreatedAt(int $direction = SORT_DESC)
    {
        return $this->orderBy(['createdAt' => $direction]);
    }

    public function orderByUpdatedAt(int $direction = SORT_DESC)
    {
        return $this->orderBy(['updatedAt' => $direction]);
    }
}

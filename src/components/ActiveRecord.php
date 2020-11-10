<?php

declare(strict_types=1);

namespace EvgeniDev\Yii2\OAuth2Server\Components;

use ReflectionClass;
use Yii;
use yii\base\Model;
use yii\db\Expression;

/**
 * Extended ActiveRecord class.
 *
 * @method hasMany($class, array $link) see [[BaseActiveRecord::hasMany()]] for more info
 * @method hasOne($class, array $link) see [[BaseActiveRecord::hasOne()]] for more info
 */
abstract class ActiveRecord extends \yii\db\ActiveRecord
{
    /**
     * Class table.
     */
    public static function tableName(): string
    {
        return (new ReflectionClass(static::class))
            ->getShortName();
    }

    /**
     * {@inheritDoc}.
     */
    public function beforeSave($insert)
    {
        if (false === parent::beforeSave($insert)) {
            return false;
        }

        // PostgreSQL не позволяет малыми силами реализовать ON UPDATE timestamp() так же как это можно в MySQL.
        if (false === $this->getIsNewRecord()) {
            $this->setAttribute('updatedAt', new Expression('NOW()'));
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public static function deleteAll($condition = null, $params = []): int
    {
        return static::getDb()
            ->createCommand()
            ->update(static::tableName(), ['deletedAt' => new Expression('NOW()')], $condition, $params)
            ->execute();
    }

    /**
     * Load data from Model.
     */
    public function loadForm(Model $model): self
    {
        $this->setAttributes($model->getAttributes());

        if (null !== $user = Yii::$app->getUser()->getIdentity()) {
            $this->setCreatedBy($user->getID());
        }

        return $this;
    }

    public function getID(): string
    {
        return $this->getAttribute('ID');
    }

    public function setID(string $id): self
    {
        $this->setAttribute('ID', $id);

        return $this;
    }

    public function getCreatedAt(string $format = null): string
    {
        return $format === null
            ? $this->getAttribute('createdAt')
            : date($format, strtotime($this->getAttribute('createdAt')));
    }

    public function getUpdatedAt(string $format = null): string
    {
        return $format === null
            ? $this->getAttribute('updatedAt')
            : date($format, strtotime($this->getAttribute('updatedAt')));
    }

    public function getCreatedBy(): ?string
    {
        return $this->getAttribute('createdBy');
    }

    public function setCreatedBy($id = null): self
    {
        $this->setAttribute('createdBy', $id);

        return $this;
    }
}

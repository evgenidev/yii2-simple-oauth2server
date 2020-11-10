<?php

declare(strict_types=1);

namespace EvgeniDev\Yii2\OAuth2Server\Exceptions;

use yii\base\Model;

/**
 * Validation exception.
 */
final class ValidationException extends \yii\base\Exception
{
    protected $model;

    /**
     * {@inheritDoc}
     */
    public function __construct(Model $model, string $message = 'Validation error')
    {
        $this->model = $model;

        parent::__construct($message);
    }

    /**
     * Returns validation errors.
     *
     * @see Model::getErrors()
     */
    public function getErrors(): array
    {
        return $this->model->getErrors();
    }
}

<?php

declare(strict_types=1);

namespace EvgeniDev\Yii2\OAuth2Server\Exceptions;

use Yii;

/**
 * Unexpected condition.
 */
final class InvalidConditionException extends \yii\base\Exception
{
    /**
     * {@inheritDoc}
     */
    public function __construct(string $message = '', ...$args)
    {
        parent::__construct($message, ...$args);
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return Yii::t('exception', 'Invalid condition');
    }
}

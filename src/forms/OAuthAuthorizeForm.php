<?php

declare(strict_types=1);

namespace EvgeniDev\Yii2\OAuth2Server\Forms;

/**
 * Oauth authorization form.
 */
class OAuthAuthorizeForm extends \yii\base\Model
{
    public $redirectUri
        = '';

    public $clientID
        = '';

    public $responseType
        = 'code';

    public $state
        = null;

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [['redirectUri', 'clientID'], 'required'],
            [['redirectUri', 'clientID', 'responseType', 'state'], 'string'],
        ];
    }
}

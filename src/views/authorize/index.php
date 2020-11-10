<?php

declare(strict_types=1);

use EvgeniDev\Yii2\OAuth2Server\Module;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this \yii\web\View
 * @var $client \EvgeniDev\Yii2\OAuth2Server\Records\OAuthClient
 * @var $form \EvgeniDev\Yii2\OAuth2Server\Forms\OAuthAuthorizeForm
 * @var $state string
 */

$this->setTitle(Module::t('app', 'authorize.title'));

?>

<div class="d-flex justify-content-center mt-50">
    <div class="row">
        <div class="col-12 mt-100">
            <div class="signup__content">
                <?php $activeForm = ActiveForm::begin([
                    'id' => 'oauth-form',
                    'enableAjaxValidation' => true,
                ]);?>
                <div class="row">
                    <?=$activeForm
                        ->field($form, 'redirectUri')
                        ->hiddenInput([
                            'value' => $client->getRedirectUri(),
                        ])
                        ->label(false)?>
                    <?=$activeForm
                        ->field($form, 'clientID')
                        ->hiddenInput([
                            'value' => $client->getClientID(),
                        ])
                        ->label(false)?>
                    <?php if (false === empty($state)): ?>
                        <?=$activeForm
                            ->field($form, 'state')
                            ->hiddenInput([
                                'value' => $state,
                            ])
                            ->label(false)?>
                    <?php endif; ?>
                    <div>
                        <?=Module::t('app', 'approve.data.transfer').' "'.$client->getName().'" ';?>
                    </div>
                </div>
                <div class="d-flex justify-content-center mt-30">
                    <?=Html::submitButton(Module::t('app', 'approve.btn'), [
                        'class' => 'btn btn-primary btn-green',
                        'name' => 'approve',
                        'value' => 'approve',
                    ])?>
                    <?=Html::submitButton(Module::t('app', 'disapprove.btn'), [
                        'class' => 'btn btn-primary btn-grey ml-20',
                        'name' => 'deny',
                        'value' => 'deny',
                    ])?>
                </div>
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
</div>

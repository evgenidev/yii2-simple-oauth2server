<?php

declare(strict_types=1);

namespace EvgeniDev\Yii2\OAuth2Server;

use yii\console\Application;
use yii\i18n\PhpMessageSource;
use Yii;

/**
 * OAuth2 server Module.
 */
class Module extends \yii\base\Module implements \yii\base\BootstrapInterface
{
    public $accessTokenLifetime;
    public $identityClass;
    public $authorizeViewPath;

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        $this->controllerNamespace = 'EvgeniDev\Yii2\OAuth2Server\Controllers';
        parent::init();

        if (empty($this->layout)) {
            $this->layout = 'main.php';
        }

        $this->registerTranslations();
    }

    /**
     * {@inheritDoc}
     */
    public function bootstrap($app)
    {
        if ($app instanceof Application) {
            $this->controllerNamespace = 'EvgeniDev\Yii2\OAuth2Server\Commands';
        }
    }

    /**
     * Translations.
     */
    public function registerTranslations()
    {
        if(!isset(Yii::$app->get('i18n')->translations['modules/oauth2/*'])) {
            Yii::$app->get('i18n')->translations['modules/oauth2/*'] = [
                'class' => PhpMessageSource::class,
                'basePath' => __DIR__.'/messages',
                'fileMap' => [
                    'modules/oauth2/app' => 'app.php',
                ],
            ];
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function t($category, $message, $params = [], $language = null): string
    {
        return Yii::t("modules/oauth2/{$category}", $message, $params, $language);
    }
}
<?php

declare(strict_types=1);

namespace EvgeniDev\Yii2\OAuth2Server\Components;

use Yii;
use yii\web\Response;

/**
 * Abstract extended Controller.
 */
abstract class Controller extends \yii\web\Controller
{
    /**
     * В отличие от стандартного функционала Yii, данный метод возвращает Response,
     * позволяя добиться чёткой статической типизации действий контроллеров, а так же
     * не требует указания названия шаблона.
     *
     * {@inheritDoc}
     *
     * @param string|array|null $view Название шаблона представления или данные, которые необходимо передать в шаблон
     * представления по-умолчанию.
     */
    public function render($view = null, $params = []): Response
    {
        $response = Yii::$app->getResponse();
        $response->data = $view === null || is_array($view)
            ? parent::render("@views/{$this->action->controller->id}", $view ?: [])
            : parent::render($view, $params);

        return $response;
    }
}
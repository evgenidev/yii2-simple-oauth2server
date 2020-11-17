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
     * Extended render function.
     * 
     * {@inheritDoc}
     */
    public function render($view = null, $params = []): Response
    {
        $response = Yii::$app->getResponse();

        $response->data = $view === null || is_array($view)
            ? parent::render("@views/{$this->action->controller->id}", $view ?: [])
            : parent::render($view, $params);

        return $response;
    }

    /**
     * Format data.
     */
    public function response(array $data, int $httpStatusCode = 200): Response
    {
        $response = Yii::$app->getResponse();
        $response->format = $this->module->responseFormat;
        $response->data = $data;
        $response->statusCode = $httpStatusCode;

        return $response;
    }
}
<?php

namespace webapi\controllers;

use src\services\WebsocketHealthChecker;
use webapi\behaviors\HttpBasicAuthHealthCheck;
use yii\rest\Controller;
use yii\web\Response;
use Yii;

/**
 * Class HealthController
 * @package webapi\controllers
 *
 *
 */

class HealthController extends Controller
{
    public function init()
    {
        parent::init();
        Yii::$app->user->enableSession = false;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBasicAuthHealthCheck::class,
        ];
        $behaviors['contentNegotiator'] = [
            'class' => 'yii\filters\ContentNegotiator',
            'formats' => [
                'application/plain' => \yii\web\Response::FORMAT_RAW,
            ]
        ];
        return $behaviors;
    }

    /**
     * @return array
     */
    private function getStatusList(): array
    {
        $responseJson = [];
        $has_error = false;

        $responseJson['mysql'] = false;
        try {
            $connection = Yii::$app->db;
            $connection->attributes[\PDO::ATTR_TIMEOUT] = 1;      // setting mysql PDO connection timeout to 1 sec
            $connection->open();
            if ($connection->pdo == null) {
                $has_error = true;
            } else {
                $responseJson['mysql'] = true;
            }
        } catch (\Throwable $e) {
            $has_error = true;
        }

        $responseJson['postgresql'] = false;
        try {
            $connection = Yii::$app->db_postgres;
            $connection->attributes[\PDO::ATTR_TIMEOUT] = 1;      // setting postgresql PDO connection timeout to 1 sec
            $connection->open();
            if ($connection->pdo == null) {
                $has_error = true;
            } else {
                $responseJson['postgresql'] = true;
            }
        } catch (\Throwable $e) {
            $has_error = true;
        }

        try {
            $connection = Yii::$app->redis;
            $connection->connectionTimeout = 1;
            $connection->dataTimeout = 1;
            $connection->open();
            $responseJson['redis'] = true;
        } catch (\Throwable $e) {
            $responseJson['redis'] = false;
            $has_error = true;
        }

        return ['json' => $responseJson, 'hasError' => $has_error];
    }

    /**
     * @api {get, post} /health-check Get health check
     * @apiVersion 0.1.0
     * @apiName HealthCheck Sales
     * @apiGroup App
     * @apiPermission Authorized User
     * @apiDescription If username is empty in config file then HttpBasicAuth is disabled.
     *
     * @apiHeader {string} Authorization    Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiSuccess {Array} data components health check passed statuses ("true" or "false")
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  {
     *      "mysql": true,
     *      "postgresql": true,
     *      "redis": true
     *  }
     *
     * @apiError ServiceUnavailable HTTP 503
     *
     * @apiErrorExample Error-Response:
     *  HTTP/1.1 503 Service Unavailable
     *  {
     *      "mysql": true,
     *      "postgresql": false,
     *      "redis": true
     *  }
     *
     *
     * @return array | string
     * @throws \Throwable
     */

    public function actionJson()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = $this->getStatusList();

        if (!empty($response['hasError'])) {
            Yii::$app->response->setStatusCode(503);
        }

        return $response['json'] ?? [];
    }

    public function actionWs()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        try {
            return (new WebsocketHealthChecker())->check(
                env('CONSOLE_CONFIG_PARAMS_WEBSOCKETSERVER_HOST'),
                env('CONSOLE_CONFIG_PARAMS_WEBSOCKETSERVER_PORT'),
                1
            );
        } catch (\Throwable $e) {
            return [
                'ws' => 'Error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * @api {get, post} /health-check/metrics Get health check metrics text
     * @apiVersion 0.1.0
     * @apiName HealthCheck Sales Metrics
     * @apiGroup App
     * @apiPermission Authorized User
     * @apiDescription If username is empty in config file then HttpBasicAuth is disabled.
     *
     * @apiHeader {string} Authorization    Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiSuccess {string} metrics in plain text format containing components health statuses ("1" for OK, "0" for failed)
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  healthcheck_status{name="mysql"} 1
     *  healthcheck_status{name="postgresql"} 1
     *  healthcheck_status{name="redis"} 1
     *
     * @apiError ServiceUnavailable HTTP 503
     *
     * @apiErrorExample Error-Response:
     *  HTTP/1.1 503 Service Unavailable
     *  healthcheck_status{name="mysql"} 1
     *  healthcheck_status{name="postgresql"} 0
     *  healthcheck_status{name="redis"} 1
     *
     *
     * @return string
     * @throws \Throwable
     */

    public function actionText(): string
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'text/plain; charset=utf-8');

        $response = $this->getStatusList();

        if (!empty($response['hasError'])) {
            Yii::$app->response->setStatusCode(503);
        }

        $responseData = [];
        if (!empty($response['json'])) {
            foreach ($response['json'] as $key => $value) {
                $responseData[] = 'healthcheck_status{name="' . $key . '"} ' . (int)$value;
            }
        }
        return implode(PHP_EOL, $responseData);
    }


    /**
     * @return array
     * @description Dummy action in case of missing location /health-check in nginx config
     */
    public function actionDummy()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
//        return new NotFoundHttpException('Invalid route: Create new NginX config location for /health-check to run health-check php script instead index.php', 100);
        return [
            'message' => 'Error: Invalid route. Create new NginX config location for /health-check to run health-check php script instead index.php',
            'code' => 401,
        ];
    }
}

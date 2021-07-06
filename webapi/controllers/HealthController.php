<?php

namespace webapi\controllers;

use webapi\behaviors\HttpBasicAuthHealthCheck;
use yii\rest\Controller;
use yii\helpers\VarDumper;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * Class HealthCheckController
 * @package webapi\controllers
 *
 *
 */

class HealthController extends Controller
{

    public function init()
    {
//VarDumper::dump($_SERVER); die;
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
                'application/json' => \yii\web\Response::FORMAT_JSON,
            ]
        ];

        return $behaviors;
    }

    /**
     * @api {get, post} /health-check Health check action
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
     * @apiSuccess {Array} data components statuses ("true" or "false")
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  {
     *      "mysql": true,
     *      "postgresql": true,
     *      "redis": true
     *  }
     *
     * @apiError Service Unavailable 503
     *
     * @apiErrorExample Success-Response:
     *  HTTP/1.1 503 Service Unavailable
     *  {
     *      "mysql": true,
     *      "postgresql": false,
     *      "redis": true
     *  }
     *
     *
     * @return array
     * @throws BadRequestHttpException
     * @throws \Throwable
     */

    public function actionIndex()
    {
        $response = [];

        try {
            $connection = Yii::$app->db;
            $connection->attributes[\PDO::ATTR_TIMEOUT] = 1;      // setting mysql PDO connection timeout to 1 sec
            $connection->open();
            if ($connection->pdo == null) {
                $response['mysql'] = false;
            } else {
                $response['mysql'] = true;
            }
        } catch (\Throwable $e) {
            $response['mysql'] = false;
        }

        try {
            $connection = Yii::$app->db_postgres;
            $connection->attributes[\PDO::ATTR_TIMEOUT] = 1;      // setting postgresql PDO connection timeout to 1 sec
//            $connection = new Yii\db\Connection($config);
            $connection->open();
            if ($connection->pdo == null) {
                $response['postgresql'] = false;
            } else {
                $response['postgresql'] = true;
            }
        } catch (\Throwable $e) {
            $response['postgresql'] = false;
        }

        try {
            $connection = Yii::$app->redis;
            $connection->connectionTimeout = 1;
            $connection->dataTimeout = 1;
            $connection->open();
            $response['redis'] = true;
        } catch (\Throwable $e) {
            $response['redis'] = false;
        }

        foreach ($response as $passed) {
            if (!$passed) {
                Yii::$app->response->setStatusCode(503);
            }
        }

        return $response;
    }

    public function actionDummy()
    {
        //return new NotFoundHttpException('Invalid route: Create new NginX config location for /health-check to run health-check php script instead index.php', 100);
        return [
            'message' => 'Error: Invalid route. Create new NginX config location for /health-check to run health-check php script instead index.php',
            'code' => 401,
        ];
    }
}

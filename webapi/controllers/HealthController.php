<?php

namespace webapi\controllers;

use webapi\behaviors\HttpBasicAuthHealthCheck;
use yii\rest\Controller;
use yii\web\Response;
use yii\helpers\VarDumper;
use Yii;
use yii\web\NotFoundHttpException;

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
        Yii::$app->response->format = Response::FORMAT_RAW;
        $response = '';
        $tests_error = false;

        try {
            $connection = Yii::$app->db;
            $connection->attributes[\PDO::ATTR_TIMEOUT] = 1;      // setting mysql PDO connection timeout to 1 sec
            $connection->open();
            if ($connection->pdo == null) {
                $response .= 'healthcheck_status{name="mysql"} 0';
                $tests_error = true;
            } else {
                $response .= 'healthcheck_status{name="mysql"} 1';
            }
        } catch (\Throwable $e) {
            $response .= 'healthcheck_status{name="mysql"} 0';
            $tests_error = true;
        }
        $response .= "\r\n";
        try {
            $connection = Yii::$app->db_postgres;
            $connection->attributes[\PDO::ATTR_TIMEOUT] = 1;      // setting postgresql PDO connection timeout to 1 sec
            $connection->open();
            if ($connection->pdo == null) {
                $response .= 'healthcheck_status{name="postgresql"} 0';
                $tests_error = true;
            } else {
                $response .= 'healthcheck_status{name="postgresql"} 1';
            }
        } catch (\Throwable $e) {
            $response .= 'healthcheck_status{name="postgresql"} 0';
            $tests_error = true;
        }
        $response .= "\r\n";

        try {
            $connection = Yii::$app->redis;
            $connection->connectionTimeout = 1;
            $connection->dataTimeout = 1;
            $connection->open();
            $response .= 'healthcheck_status{name="redis"} 1';
        } catch (\Throwable $e) {
            $response .= 'healthcheck_status{name="redis"} 0';
            $tests_error = true;
        }
        $response .= "\n";

        if ($tests_error) {
                Yii::$app->response->setStatusCode(503);
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

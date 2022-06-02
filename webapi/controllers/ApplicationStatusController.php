<?php

namespace webapi\controllers;

use yii\rest\Controller;
use yii\web\Response;
use yii\filters\ContentNegotiator;

/**
 * Class ApplicationStatusController
 *
 * @package webapi\controllers
 */
class ApplicationStatusController extends Controller
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'only' => ['ping', 'health'],
                'formats' => [
                    '*/*' => Response::FORMAT_JSON
                ]
            ]
        ];
    }

    /**
     * @api {get} /application-status/ping Get application status
     * @apiVersion 1.0.0
     * @apiName Ping
     * @apiGroup ApplicationStatus
     * @apiPermission All
     * @apiDescription Action that returns service availability
     *
     * @apiSuccess {string} app Application name
     * @apiSuccess {bool} availability Boolean indicator of application availability
     * @apiSuccess {string} datetime Response datetime in format `Y-m-d H:i:s`
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *     'app' => 'api',
     *     'availability' => true,
     *     'datetime' => '2022-06-27 16:28:15'
     * }
     */
    public function actionPing()
    {
        return [
            'app' => 'api',
            'availability' => true,
            'datetime' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * @api {get} /application-status/health Get health of application components
     * @apiVersion 1.0.0
     * @apiName Health
     * @apiGroup ApplicationStatus
     * @apiPermission All
     * @apiDescription Action that return health statuses of application components
     *
     * @apiSuccess {string} app Application name
     * @apiSuccess {string} type Service type
     * @apiSuccess {string} service Service name
     * @apiSuccess {bool} status Service status indicator
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * [
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "db",
     *         "status": "ok"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "db_slave",
     *         "status": "ok"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "db_postgres",
     *         "status": "ok"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "redis",
     *         "status": "ok"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "mailer",
     *         "status": "ok"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "communication",
     *         "status": "ok"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "airSearch",
     *         "status": "ok"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "rChat",
     *         "status": "ok"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "chatBot",
     *         "status": "ok"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "travelServices",
     *         "status": "ok"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "queueSmsJob",
     *         "status": "ok"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "queueEmailJob",
     *         "status": "ok"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "queuePhoneCheck",
     *         "status": "ok"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "queueJob",
     *         "status": "ok"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "queueSystemServices",
     *         "status": "ok"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "queueClientChatJob",
     *         "status": "ok"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "queueVirtualCron",
     *         "status": "ok"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "queueLeadRedial",
     *         "status": "ok"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "telegram",
     *         "status": "ok"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "gaRequestService",
     *         "status": "ok"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "centrifugo",
     *         "status": "ok"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "callAntiSpam",
     *         "status": "ok"
     *     }
     * ]
     *
     * @apiErrorExample Error-Response:
     * HTTP/1.1 500 OK
     * [
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "db",
     *         "status": "ok"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "db_slave",
     *         "status": "error"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "db_postgres",
     *         "status": "error"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "redis",
     *         "status": "ok"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "mailer",
     *         "status": "error"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "communication",
     *         "status": "error"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "airSearch",
     *         "status": "ok"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "rChat",
     *         "status": "error"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "chatBot",
     *         "status": "ok"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "travelServices",
     *         "status": "error"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "queueSmsJob",
     *         "status": "error"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "queueEmailJob",
     *         "status": "error"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "queuePhoneCheck",
     *         "status": "error"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "queueJob",
     *         "status": "error"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "queueSystemServices",
     *         "status": "error"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "queueClientChatJob",
     *         "status": "error"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "queueVirtualCron",
     *         "status": "error"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "queueLeadRedial",
     *         "status": "error"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "telegram",
     *         "status": "error"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "gaRequestService",
     *         "status": "error"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "centrifugo",
     *         "status": "ok"
     *     },
     *     {
     *         "app": "app",
     *         "type": "component",
     *         "service": "callAntiSpam",
     *         "status": "error"
     *     }
     * ]
     */
    public function actionHealth(): array
    {
        $result = [
            [
                'app' => 'app',
                'type' => 'component',
                'service' => 'db',
                'status' => \Yii::$app->applicationStatus->dbStatus()
            ],
            [
                'app' => 'app',
                'type' => 'component',
                'service' => 'db_slave',
                'status' => \Yii::$app->applicationStatus->dbSlaveStatus()
            ],
            [
                'app' => 'app',
                'type' => 'component',
                'service' => 'db_postgres',
                'status' => \Yii::$app->applicationStatus->dbPostgresStatus()
            ],
            [
                'app' => 'app',
                'type' => 'component',
                'service' => 'redis',
                'status' => \Yii::$app->applicationStatus->redisStatus()
            ],
            [
                'app' => 'app',
                'type' => 'component',
                'service' => 'mailer',
                'status' => \Yii::$app->applicationStatus->mailerStatus()
            ],
            [
                'app' => 'app',
                'type' => 'component',
                'service' => 'communication',
                'status' => \Yii::$app->applicationStatus->communicationStatus()
            ],
            [
                'app' => 'app',
                'type' => 'component',
                'service' => 'airSearch',
                'status' => \Yii::$app->applicationStatus->airSearchStatus()
            ],
            [
                'app' => 'app',
                'type' => 'component',
                'service' => 'rChat',
                'status' => \Yii::$app->applicationStatus->rChatStatus()
            ],
            [
                'app' => 'app',
                'type' => 'component',
                'service' => 'chatBot',
                'status' => \Yii::$app->applicationStatus->chatBotStatus()
            ],
//            [
//                'app' => 'app',
//                'type' => 'component',
//                'service' => 'travelServices',
//                'status' => \Yii::$app->applicationStatus->travelServicesStatus()
//            ],
            [
                'app' => 'app',
                'type' => 'component',
                'service' => 'queueSmsJob',
                'status' => \Yii::$app->applicationStatus->queueSmsJobStatus()
            ],
            [
                'app' => 'app',
                'type' => 'component',
                'service' => 'queueEmailJob',
                'status' => \Yii::$app->applicationStatus->queueEmailJobStatus()
            ],
            [
                'app' => 'app',
                'type' => 'component',
                'service' => 'queuePhoneCheck',
                'status' => \Yii::$app->applicationStatus->queuePhoneCheckStatus()
            ],
            [
                'app' => 'app',
                'type' => 'component',
                'service' => 'queueJob',
                'status' => \Yii::$app->applicationStatus->queueJobStatus()
            ],
            [
                'app' => 'app',
                'type' => 'component',
                'service' => 'queueSystemServices',
                'status' => \Yii::$app->applicationStatus->queueSystemServicesStatus()
            ],
            [
                'app' => 'app',
                'type' => 'component',
                'service' => 'queueClientChatJob',
                'status' => \Yii::$app->applicationStatus->queueClientChatJobStatus()
            ],
            [
                'app' => 'app',
                'type' => 'component',
                'service' => 'queueVirtualCron',
                'status' => \Yii::$app->applicationStatus->queueVirtualCronStatus()
            ],
            [
                'app' => 'app',
                'type' => 'component',
                'service' => 'queueLeadRedial',
                'status' => \Yii::$app->applicationStatus->queueLeadRedialStatus()
            ],
            [
                'app' => 'app',
                'type' => 'component',
                'service' => 'telegram',
                'status' => \Yii::$app->applicationStatus->telegramStatus()
            ],
//            [
//                'app' => 'app',
//                'type' => 'component',
//                'service' => 'gaRequestService',
//                'status' => \Yii::$app->applicationStatus->gaRequestServiceStatus()
//            ],
            [
                'app' => 'app',
                'type' => 'component',
                'service' => 'centrifugo',
                'status' => \Yii::$app->applicationStatus->centrifugoStatus()
            ],
//            [
//                'app' => 'app',
//                'type' => 'component',
//                'service' => 'callAntiSpam',
//                'status' => \Yii::$app->applicationStatus->callAntiSpamStatus()
//            ],
        ];

        $notWorkingComponentsList = array_filter($result, function ($item) {
            return isset($item['status']) && $item['status'] !== 'ok';
        });

        if (count($notWorkingComponentsList) > 0) {
            \Yii::$app->response->statusCode = 500;
        }

        return $result;
    }
}

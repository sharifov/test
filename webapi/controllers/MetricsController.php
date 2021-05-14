<?php

namespace webapi\controllers;

use kartik\select2\ThemeDefaultAsset;
use webapi\models\PrometheusUser;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBasicAuth;
use yii\web\Controller;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 * Class PrometheusController
 * @package webapi\modules\controllers
 */
class MetricsController extends Controller
{
    public function init(): void
    {
        parent::init();
        Yii::$app->user->enableSession = false;
        $this->enableCsrfValidation = false;
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        if (Yii::$app->prometheus->useHttpBasicAuth) {
            $behaviors['authenticator'] = [
                'class' => HttpBasicAuth::class,
            ];

            $behaviors['authenticator']['auth'] = function ($username, $password) {
                $user = new PrometheusUser();
                if (!$user::login($username, $password)) {
                    $message = 'Invalid username or password for HttpBasicAuth Prometheus component';
                    Yii::warning(['message' => $message, 'username' => $username, 'endpoint' => $this->action->uniqueId, 'RemoteIP' => Yii::$app->request->getRemoteIP(),
                        'UserIP' => Yii::$app->request->getUserIP()], 'API:MetricsController:HttpBasicAuth');
                    throw new NotAcceptableHttpException($message, 10);
                }
                return $user;
            };

            $behaviors['access'] = [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ];
        }

        return $behaviors;
    }


    /**
     * @return string
     * @throws ServerErrorHttpException
     */
    public function actionIndex(): string
    {
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        $response->getHeaders()->set('Content-Type', 'text/plain');
        try {
            $response = Yii::$app->prometheus->getMetric();
        } catch (\Throwable $throwable) {
            throw new ServerErrorHttpException($throwable->getMessage(), 11);
        }

        return $response;
    }

    public function actionFlush(): string
    {
        try {
            $adapter = Yii::createObject(\Prometheus\Storage\Redis::class);
            $adapter::setDefaultOptions(Yii::$app->prometheus->redisOptions);
            $adapter->flushRedis();
        } catch (\Throwable $throwable) {
            \yii\helpers\VarDumper::dump($throwable->getMessage(), 10, true);
        }
        return PHP_EOL . 'Done';
    }

    public function actionKeys()
    {
        try {
            $redisOptions = Yii::$app->prometheus->redisOptions;
            $prometeusRedis = new \yii\redis\Connection([
                'hostname' => $redisOptions['host'],
                'port' => $redisOptions['port'],
                'database' => $redisOptions['database'],
                'password' => $redisOptions['password'],
            ]);
            \yii\helpers\VarDumper::dump($prometeusRedis->keys('*'), 20, true);
            exit();
        } catch (\Throwable $throwable) {
            \yii\helpers\VarDumper::dump($throwable->getMessage(), 10, true);
        }
    }

    public function actionRemoveByKey(string $key)
    {
        try {
            $redisOptions = Yii::$app->prometheus->redisOptions;
            $prometeusRedis = new \yii\redis\Connection([
                'hostname' => $redisOptions['host'],
                'port' => $redisOptions['port'],
                'database' => $redisOptions['database'],
                'password' => $redisOptions['password'],
            ]);

            if (!(bool) $prometeusRedis->exists($key)) {
                exit('Key not found.');
            }
            $dump = $prometeusRedis->dump($key);
            $prometeusRedis->unlink($key);

            \yii\helpers\VarDumper::dump([
                'status' => 'Metric deleted',
                'key' => $key,
                'dump' => $dump,
            ], 20, true);
            exit();
        } catch (\Throwable $throwable) {
            \yii\helpers\VarDumper::dump($throwable->getMessage(), 10, true);
        }
    }
}

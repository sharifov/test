<?php
namespace webapi\controllers;

use webapi\models\PrometheusUser;
use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBasicAuth;
use yii\web\Controller;
use yii\web\NotAcceptableHttpException;
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
                    //return null;
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
}

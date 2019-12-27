<?php

namespace webapi\modules\v2\controllers;

use webapi\src\logger\ApiLogger;
use webapi\src\logger\EndDTO;
use webapi\src\logger\StartDTO;
use Yii;
use common\models\ApiUser;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\ContentNegotiator;
use yii\rest\Controller;
use yii\web\NotAcceptableHttpException;
use yii\web\Response;
use yii\base\Action;

/**
 * Class BaseController
 *
 * @property ApiLogger $logger
 */
class BaseController extends Controller
{
    private $logger;

    public function __construct($id, $module, ApiLogger $logger, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->logger = $logger;
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];
        $behaviors['authenticator'] = [
            'class' => HttpBasicAuth::class,
            'auth' => [$this, 'auth'],
        ];
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];
        return $behaviors;
    }

    /**
     * @param $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $request = Yii::$app->request;
        $user = Yii::$app->user;

        /** @var Action $action */
        $this->logger->start(
            new StartDTO([
                'data' => @json_encode($request->post()),
                'action' => $action->uniqueId,
                'userId' => $user->id,
                'ip' => $request->getRemoteIP(),
                'startTime' => microtime(true),
                'startMemory' => memory_get_usage(),
            ])
        );

        return true;
    }

    public function afterAction($action, $result)
    {
        /** @var \webapi\src\response\Response $result */
        $result = parent::afterAction($action, $result);

        $this->logger->end(
            new EndDTO([
                'result' => @json_encode($result->getResponse()),
                'endTime' => microtime(true),
                'endMemory' => memory_get_usage(),
                'profiling' => Yii::getLogger()->getDbProfiling(),
            ])
        );

        $result->addData('request', Yii::$app->request->post());
        $result->addData('technical', $this->logger->getTechnicalInfo());

        Yii::$app->response->statusCode = $result->getResponseStatusCode();

        return $result->getResponse();
    }

    public function auth($username, $password): ?ApiUser
    {
        if (!$user = ApiUser::findOne(['au_api_username' => $username])) {
            Yii::warning('API not found username: ' . $username, 'API:HttpBasicAuth:ApiUser');
            return null;
        }

        if (!$user->validatePassword($password)) {
            Yii::warning('API invalid password: ' . $password . ', username: ' . $username . ' ', 'API:HttpBasicAuth:ApiUser');
            return null;
        }

        if ($user->isDisabled()) {
            throw new NotAcceptableHttpException('ApiUser is disabled', 10);
        }

        return $user;
    }

    protected function verbs(): array
    {
        return ['*' => ['POST']];
    }
}

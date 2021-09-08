<?php

namespace webapi\modules\v1\controllers;

use common\models\ApiLog;
use common\models\ApiUser;
use common\models\Project;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use webapi\src\behaviors\ApiUserProjectAccessBehavior;
use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\ContentNegotiator;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\Response;

/**
 * Class ApiBaseController
 * @package webapi\controllers
 *
 * @property bool $debug
 * @property Project $apiProject
 * @property ApiUser $apiUser
 * @property ApiLog|null $apiLog
 *
 */
class ApiBaseController extends Controller
{
    public $apiUser;
    public $apiProject;
    public $debug = false;

    protected $apiLog;

    /**
     *
     */
    public function init()
    {
        parent::init();

        Yii::$app->user->enableSession = false;
        if (Yii::$app->request->get('debug')) {
            $this->debug = true;
        }
    }


    /**
     * @return array
     * @throws NotAcceptableHttpException
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $apiKey = Yii::$app->request->post('apiKey');



        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        if ($apiKey) {
            $behaviors['apiUserProjectAccessBehavior'] = ['class' => ApiUserProjectAccessBehavior::class, 'apiKey' => $apiKey];
        } else {
            $behaviors['authenticator'] = [
                'class' => HttpBasicAuth::class,
            ];

            $behaviors['authenticator']['auth'] = function ($username, $password) {
                $apiUser = ApiUser::findOne([
                    'au_api_username' => $username
                ]);


                if (!$apiUser) {
                    Yii::warning(['message' => 'API: not found username', 'username' => $username,
                        'endpoint' => $this->action->uniqueId,
                        'RemoteIP' => Yii::$app->request->getRemoteIP(),
                        'UserIP' => Yii::$app->request->getUserIP()], 'API:v1:HttpBasicAuth:ApiUser');
                    return null;
                }

                if (!$apiUser->validatePassword($password)) {
                    Yii::warning(['message' => 'API: invalid password', 'username' => $username,
                        'endpoint' => $this->action->uniqueId,
                        'password' => \yii\helpers\StringHelper::truncate($password, 4),
                        'RemoteIP' => Yii::$app->request->getRemoteIP(),
                        'UserIP' => Yii::$app->request->getUserIP()], 'API:v1:HttpBasicAuth:ApiUser');
                    return null;
                }


                if (!$apiUser->au_enabled) {
                    throw new NotAcceptableHttpException('ApiUser is disabled', 10);
                }
                $apiProject = Project::findOne($apiUser->au_project_id);

                $this->apiUser = $apiUser;
                $this->apiProject = $apiProject;

                return $apiUser;
            };
        }

        $behaviors['access'] = [
            'class' => AccessControl::class,
            //'only' => ['signup'],
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],
                ],
                [
                    'allow' => true,
                    'actions' => ['test'],
                    'roles' => ['?'],
                ],
            ],

        ];

        return $behaviors;
    }


    /**
     * @param array $errors
     * @return string
     */
    public function errorToString($errors = []): string
    {
        $arr_errors = [];
        foreach ($errors as $k => $v) {
            $arr_errors[] = is_array($v) ? implode(',', $v) : print_r($v, true);
        }
        return implode('; ', $arr_errors);
    }


    /**
     * @throws BadRequestHttpException
     */
    public function checkPost(): void
    {
        if (!Yii::$app->request->isPost) {
            throw new BadRequestHttpException('Not found POST request', 1);
        }
        if (!Yii::$app->request->post()) {
            throw new BadRequestHttpException('POST data request is empty', 2);
        }
    }

    /**
     * @param string $action
     * @return void
     */
    public function startApiLog(string $action = ''): void
    {
        $data = Yii::$app->request->post();
        $data['received_microtime'] = microtime(true);

        $apiLog = new ApiLog();
        $apiLog->al_request_data = @json_encode($data);
        $apiLog->al_request_dt = date('Y-m-d H:i:s');
        $apiLog->al_ip_address = Yii::$app->request->getRemoteIP();
        $apiLog->al_action = $action;

        $apiLog->start_microtime = microtime(true);
        $apiLog->start_memory_usage = memory_get_usage();


        if ($this->apiUser) {
            $apiLog->al_user_id = $this->apiUser->au_id;
        }

        if (!$apiLog->save()) {
            Yii::error(print_r($apiLog->errors, true), 'ApiBaseControl:startApiLog:ApiLog:save');
        }
        $this->apiLog = $apiLog;
    }
}

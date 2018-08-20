<?php
namespace webapi\modules\v1\controllers;

use common\models\ApiLog;
use common\models\ApiUser;
use common\models\Project;
use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\ContentNegotiator;
use yii\rest\Controller;
use yii\filters\AccessControl;
use yii\web\BadRequestHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\Response;


/**
 * Class ApiBaseController
 * @package webapi\controllers
 */
class ApiBaseController extends Controller
{

    public $apiUser;
    public $apiProject;

    /**
     *
     */
    public function init()
    {
        parent::init();
        Yii::$app->user->enableSession = false;
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

        if($apiKey) {

            $apiUser = null;
            $apiProject = Project::find()->where(['api_key' => $apiKey])->one();
            if($apiProject) {
                $apiUser = ApiUser::findOne([
                    'au_project_id' => $apiProject->id
                ]);

                if($apiUser) {

                    if($apiUser->au_enabled) {
                        $this->apiUser = $apiUser;
                        $this->apiProject = $apiProject;
                    } else {
                        throw new NotAcceptableHttpException('ApiUser is disabled', 10);
                    }
                }
            } else {
                throw new NotAcceptableHttpException('Not init Project', 9);
            }

            if(!$apiUser) {
                throw new NotAcceptableHttpException('Not init User', 8);
            }

            Yii::$app->getUser()->login($apiUser);


        } else {

            $behaviors['authenticator'] = [
                'class' => HttpBasicAuth::class,
            ];

            $behaviors['authenticator']['auth'] = function ($username, $password) {

                $apiUser = ApiUser::findOne([
                    'au_api_username' => $username
                ]);

                if (!$apiUser) return NULL;
                if (!$apiUser->validatePassword($password)) return NULL;
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
    public function errorToString($errors = [])
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
    public function checkPost()
    {
        if (!Yii::$app->request->isPost) throw new BadRequestHttpException('Not found POST request', 1);
        if (!Yii::$app->request->post()) throw new BadRequestHttpException('POST data request is empty', 2);
    }

    /**
     * @param string $action
     * @return ApiLog
     */
    public function startApiLog(string $action = '')
    {

        $apiLog = new ApiLog();
        $apiLog->al_request_data = @json_encode(Yii::$app->request->post());
        $apiLog->al_request_dt = date('Y-m-d H:i:s');
        $apiLog->al_ip_address = Yii::$app->request->getRemoteIP();
        $apiLog->al_action = $action;
        if($this->apiUser) $apiLog->al_user_id = $this->apiUser->au_id;

        if(!$apiLog->save()) Yii::error(print_r($apiLog->errors, true), 'ApiBaseControl:startApiLog:ApiLog:save');

        return $apiLog;
    }

}

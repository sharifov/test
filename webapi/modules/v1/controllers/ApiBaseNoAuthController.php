<?php
namespace webapi\modules\v1\controllers;

use common\models\ApiLog;
use Yii;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;


/**
 * Class ApiBaseNoAuthController
 * @package webapi\controllers
 *
 * @property bool $debug
 *
 */
class ApiBaseNoAuthController extends Controller
{
    public $debug = false;

    /**
     *
     */
    public function init()
    {
        parent::init();

        Yii::$app->user->enableSession = false;
        if(Yii::$app->request->get('debug')) {
            $this->debug = true;
        }
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
     * @return ApiLog
     */
    public function startApiLog(string $action = ''): ApiLog
    {

        $apiLog = new ApiLog();
        $apiLog->al_request_data = @json_encode(Yii::$app->request->post());
        $apiLog->al_request_dt = date('Y-m-d H:i:s');
        $apiLog->al_ip_address = Yii::$app->request->getRemoteIP();
        $apiLog->al_action = $action;

        $apiLog->start_microtime = microtime(true);
        $apiLog->start_memory_usage = memory_get_usage();

        if(!$apiLog->save()) {
            Yii::error(print_r($apiLog->errors, true), 'ApiBaseNoAuthControl:startApiLog:ApiLog:save');
        }

        return $apiLog;
    }

}

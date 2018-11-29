<?php
namespace webapi\modules\v1\controllers;

use common\models\Email;
use Yii;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;

class CommunicationController extends ApiBaseController
{

    public const ACTION_GET     = 'get';
    public const ACTION_sET     = 'set';
    public const ACTION_READ    = 'read';
    public const ACTION_UPDATE  = 'update';
    public const ACTION_CREATE  = 'create';
    public const ACTION_DELETE  = 'delete';

    /**
     * @api {post} /v1/communication/email Communication Email
     * @apiVersion 0.1.0
     * @apiName CommunicationEmail
     * @apiGroup Communication
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization    Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  {
     *      "action": "update",
     *      "eq_id": 127,
     *      "eq_status_id": 5,
     *  }
     *
     *
     * @return array
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     * @throws \yii\web\BadRequestHttpException
     */

    public function actionEmail(): array
    {
        $this->checkPost();
        $apiLog = $this->startApiLog($this->action->uniqueId);

        $action = Yii::$app->request->post('action');
        $eq_id = Yii::$app->request->post('eq_id');
        $eq_status_id = Yii::$app->request->post('eq_status_id');

        if(!$action) {
            throw new NotFoundHttpException('Not found action', 1);
        }

        if(!$eq_id) {
            throw new NotFoundHttpException('Not found eq_id', 2);
        }

        if(!$eq_status_id) {
            throw new NotFoundHttpException('Not found eq_status_id', 3);
        }

        // $eq_project_id = Yii::$app->request->post('eq_project_id');

        try {

            $email = Email::findOne(['e_communication_id' => $eq_id]);
            if($email) {

                if($action == self::ACTION_UPDATE && $eq_status_id > 0) {
                    $email->e_status_id = $eq_status_id;
                    if($eq_status_id == Email::STATUS_DONE) {
                        $email->e_status_done_dt = date('Y-m-d H:i:s');
                    }

                    if(!$email->save()) {
                        Yii::error(VarDumper::dumpAsString($email->errors), 'API:Communication:Email:save');
                    }
                }

                $response['email'] = $email->e_id;
            } else {
                $response['error'] = 'Not found Communication ID ('.$eq_id.')';
                $response['error_code'] = 4;
            }


        } catch (\Throwable $e) {

            Yii::error($e->getTraceAsString(), 'API:Communication:Email:try');
            if (Yii::$app->request->get('debug')) {
                $message = $e->getTraceAsString();
            } else {
                $message = $e->getMessage() . ' (code:' . $e->getCode() . ', line: ' . $e->getLine() . ')';
            }

            $response['error'] = $message;
            $response['error_code'] = 10;

        }

        $responseData = [];

        if (isset($response['error']) && $response['error']) {

        } else {
            $responseData['status']     = 200;
            $responseData['name']       = 'Success';
            $responseData['code']       = 0;
            $responseData['message']    = '';
        }

        $responseData['data']['response'] = $response;
        // $responseData['data']['request']                = $modelLead;

        $responseData = $apiLog->endApiLog($responseData);

        if (isset($response['error']) && $response['error']) {
            $json = @json_encode($response['error']);
            if (isset($response['error_code']) && $response['error_code']) {
                $error_code = $response['error_code'];
            } else {
                $error_code = 0;
            }
            throw new UnprocessableEntityHttpException($json, $error_code);
        }

        return $responseData;
    }

}

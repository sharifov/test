<?php
namespace webapi\modules\v1\controllers;

use common\components\CommunicationService;
use common\models\Email;
use Yii;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
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

    public const TYPE_UPDATE_EMAIL_STATUS = 'update_email_status';
    public const TYPE_NEW_MESSAGES_RECEIVED = 'new_messages_received';

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
     *      "type": "update_email_status",
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

        //$action = Yii::$app->request->post('action');
        $type = Yii::$app->request->post('type');

        /*if(!$action) {
            throw new NotFoundHttpException('Not found action', 1);
        }*/

        if(!$type) {
            throw new NotFoundHttpException('Not found type', 1);
        }


        switch ($type) {
            case self::TYPE_UPDATE_EMAIL_STATUS : $response = $this->updateEmailStatus();
                break;
            case self::TYPE_NEW_MESSAGES_RECEIVED : $response = $this->newMessagesReceived();
                break;
            default: throw new BadRequestHttpException('Invalid type', 2);
        }

        $responseData = [];

        if (isset($response['error']) && $response['error']) {

        } else {
            $responseData = [
                'status'    => 200,
                'name'      => 'Success',
                'code'      => 0,
                'message'   => ''
            ];
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


    private function updateEmailStatus()
    {
        $eq_id = (int) Yii::$app->request->post('eq_id');
        $eq_status_id = (int) Yii::$app->request->post('eq_status_id');
        // $eq_project_id = Yii::$app->request->post('eq_project_id');

        try {

            if(!$eq_id) {
                throw new NotFoundHttpException('Not found eq_id', 11);
            }

            if(!$eq_status_id) {
                throw new NotFoundHttpException('Not found eq_status_id', 12);
            }

            $email = Email::findOne(['e_communication_id' => $eq_id]);
            if($email) {

                if($eq_status_id > 0) {
                    $email->e_status_id = $eq_status_id;
                    if($eq_status_id === Email::STATUS_DONE) {
                        $email->e_status_done_dt = date('Y-m-d H:i:s');
                    }

                    if(!$email->save()) {
                        Yii::error(VarDumper::dumpAsString($email->errors), 'API:Communication:updateEmailStatus:Email:save');
                    }
                }

                $response['email'] = $email->e_id;
            } else {
                $response['error'] = 'Not found Communication ID ('.$eq_id.')';
                $response['error_code'] = 13;
            }


        } catch (\Throwable $e) {
            Yii::error($e->getTraceAsString(), 'API:Communication:updateEmailStatus:Email:try');
            $message = $this->debug ? $e->getTraceAsString() : $e->getMessage() . ' (code:' . $e->getCode() . ', line: ' . $e->getLine() . ')';
            $response['error'] = $message;
            $response['error_code'] = 15;
        }

        return $response;
    }



    private function newMessagesReceived()
    {
        $response = [];

        try {

            /** @var CommunicationService $communication */
            $communication = Yii::$app->communication;

            $filter = [];
            $dateTime = null;

            $filter['last_dt'] = '';

            /*$email_to = Yii::$app->request->post('email_to');
        $email_from = Yii::$app->request->post('email_from');
        $limit = Yii::$app->request->post('limit');
        $offset = Yii::$app->request->post('offset');
        $new = Yii::$app->request->post('new');
        $last_id = Yii::$app->request->post('last_id');
        $last_dt = Yii::$app->request->post('last_dt');*/

            $res = $communication->mailGetMessages($filter);




            if(isset($res['error']) && $res['error']) {

                $response['error'] = 'Error mailGetMessages';
                $response['error_code'] = 13;


            } elseif(isset($res['data']) && $res['data'] && \is_array($res['data'])) {

                /*
                * @property int $ei_id
                * @property string $ei_email_to
                * @property string $ei_email_from
                * @property string $ei_email_subject
                * @property string $ei_email_text
                * @property string $ei_email_category
                * @property int $ei_project_id
                * @property bool $ei_new
                * @property bool $ei_deleted
                * @property string $ei_created_dt
                * @property string $ei_updated_dt
                * @property string $ei_ref_mess_ids
                * @property string $ei_message_id
                    */

                foreach ($res['data'] as $mail) {
                    $email = new Email();

                    $email->e_type_id = Email::TYPE_INBOX;
                    $email->e_status_id = Email::STATUS_DONE;
                    $email->e_is_new = true;

                    $email->e_email_to = $mail['ei_email_to'];
                    $email->e_email_from = $mail['ei_email_from'];
                    $email->e_email_subject = $mail['ei_email_subject'];
                    $email->e_project_id = $mail['ei_project_id'];
                    $email->e_email_body_html = $mail['ei_email_body_text'];
                    $email->e_created_dt = $mail['ei_created_dt'];

                    if(!$email->save()) {
                        Yii::error(VarDumper::dumpAsString($email->errors), 'API:Communication:newMessagesReceived:Email:save');
                    }
                }

                /*if($eq_status_id > 0) {
                    $email->e_status_id = $eq_status_id;
                    if($eq_status_id === Email::STATUS_DONE) {
                        $email->e_status_done_dt = date('Y-m-d H:i:s');
                    }


                }*/
            }


        } catch (\Throwable $e) {
            Yii::error($e->getTraceAsString(), 'API:Communication:newMessagesReceived:Email:try');
            $message = $this->debug ? $e->getTraceAsString() : $e->getMessage() . ' (code:' . $e->getCode() . ', line: ' . $e->getLine() . ')';
            $response['error'] = $message;
            $response['error_code'] = 15;
        }

        return $response;
    }

}

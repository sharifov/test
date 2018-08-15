<?php
namespace webapi\modules\v1\controllers;

use common\models\EmployeeContactInfo;
use common\models\Quote;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;


class QuoteController extends ApiBaseController
{


    /**
     *
     * @api {post} /v1/quote/getinfo Get Quote
     * @apiVersion 0.1.0
     * @apiName GetQuote
     * @apiGroup Quotes
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization    Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{13}}           uid      Quote UID
     * @apiParam {string}               apiKey   API Key
     *
     *
     * @apiParamExample {json} Request-Example:
     * {
     *      "uid": "5b6d03d61f078",
     *      "apiKey": "d190c378e131ccfd8a889c8ee8994cb55f22fbeeb93f9b99007e8e7ecc24d0dd"
     * }
     *
     * @apiSuccess {Integer} response_id    Response Id
     * @apiSuccess {DateTime} request_dt    Request Date & Time
     * @apiSuccess {DateTime} response_dt   Response Date & Time
     * @apiSuccess {Array} data Data Array
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *
     * @apiError UserNotFound The id of the User was not found.
     *
     * @apiErrorExample Error-Response:
     *   HTTP/1.1 404 Not Found
     *   {
     *       "name": "Not Found",
     *       "message": "Not found Quote UID: 30",
     *       "code": 2,
     *       "status": 404,
     *       "type": "yii\\web\\NotFoundHttpException"
     *   }
     *
     *
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     */



    public function actionGetInfo()
    {

        $this->checkPost();
        $apiLog = $this->startApiLog($this->action->uniqueId);


        $uid = Yii::$app->request->post('uid');

        if(!$uid) {
            throw new BadRequestHttpException('Not found UID on POST request', 1);
        }

        $model = Quote::find()->where(['uid' => $uid])->one();

        if(!$model) {
            throw new NotFoundHttpException('Not found Quote UID: '.$uid, 2);
        }


        $response = [
            'status' => 'Failed',
            'itinerary' => [],
            'errors' => []
        ];

        try {

            $result['status'] = ($model->status != $model::STATUS_DECLINED) ? 'Success' : 'Failed';

            $sellerContactInfo = EmployeeContactInfo::findOne([
                'employee_id' => $model->lead->employee_id,
                'project_id' => $model->lead->project_id
            ]);

            $result['agentName'] = $model->lead->employee->username;
            $result['agentEmail'] = $sellerContactInfo ? $sellerContactInfo->email_user : $model->lead->project->contactInfo->email;

            $result['agentDirectLine'] = ($sellerContactInfo !== null) ? $sellerContactInfo->direct_line : sprintf('+1 %s', $model->lead->project->contactInfo->phone);
            $result['itinerary']['tripType'] = $model->trip_type;
            $result['itinerary']['mainCarrier'] = ($model->getMainCarrier()) ? $model->getMainCarrier()->name : $model->main_airline_code;
            $result['itinerary']['trips'] = $model->getTrips();
            $result['itinerary']['price'] = $model->quotePrice();

            // TODO: Quote::STATUS_SEND
            /*if ($model->status == Quote::STATUS_SEND) {
                $model->status = Quote::STATUS_OPENED;
                $model->has_opened_date = date('Y-m-d h:m:i');
                $model->save();
            }*/


        } catch (\Throwable $e) {

            Yii::error($e->getTraceAsString(), 'API:Quote:get:try');
            if(Yii::$app->request->get('debug')) $message = ($e->getTraceAsString());
            else $message = $e->getMessage().' (code:'.$e->getCode().', line: '.$e->getLine().')';

            $response['error'] = $message;
            $response['error_code'] = 30;
        }


        $responseData = $response;
        $responseData = $apiLog->endApiLog($responseData);

        if(isset($response['error']) && $response['error']) {
            $json = @json_encode($response['error']);
            if(isset($response['error_code']) && $response['error_code']) $error_code = $response['error_code'];
            else $error_code = 0;
            throw new UnprocessableEntityHttpException($json, $error_code);
        }

        return $responseData;
    }


}

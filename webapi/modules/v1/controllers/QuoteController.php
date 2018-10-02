<?php

namespace webapi\modules\v1\controllers;

use common\components\BackOffice;
use common\models\EmployeeContactInfo;
use common\models\Lead;
use common\models\LeadLog;
use common\models\local\LeadLogMessage;
use common\models\Quote;
use common\models\QuotePrice;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;


class QuoteController extends ApiBaseController
{


    /**
     *
     * @api {post} /v1/quote/get-info Get Quote
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
     * @apiParam {string{13}}       uid      Quote UID
     * @apiParam {string}           [apiKey]   API Key for Project (if not use Basic-Authorization)
     *
     *
     * @apiParamExample {json} Request-Example:
     * {
     *      "uid": "5b6d03d61f078",
     *      "apiKey": "d190c378e131ccfd8a889c8ee8994cb55f22fbeeb93f9b99007e8e7ecc24d0dd"
     * }
     *
     * @apiSuccess {string} status    Status
     * @apiSuccess {object} itinerary Itinerary List
     *
     * @apiSuccess {array} errors    Errors
     * @apiSuccess {string} uid    Quote UID
     * @apiSuccess {string} agentName    Agent Name
     * @apiSuccess {string} agentEmail    Agent Email
     * @apiSuccess {string} agentDirectLine    Agent DirectLine
     *
     * @apiSuccess {string} action    Action
     * @apiSuccess {integer} response_id    Response Id
     * @apiSuccess {DateTime} request_dt    Request Date & Time
     * @apiSuccess {DateTime} response_dt   Response Date & Time
     *
     * "errors": [],
     * "uid": "5b7424e858e91",
     * "agentName": "admin",
     * "agentEmail": "assistant@wowfare.com",
     * "agentDirectLine": "+1 888 946 3882",
     * "action": "v1/quote/get-info",
     *
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *
     *
     * {
     * "status": "Success",
     * "itinerary": {
     * "tripType": "OW",
     * "mainCarrier": "WOW air",
     * "trips": [
     * {
     * "segments": [
     * {
     * "carrier": "WW",
     * "airlineName": "WOW air",
     * "departureAirport": "BOS",
     * "arrivalAirport": "KEF",
     * "departureDateTime": {
     * "date": "2018-09-19 19:00:00.000000",
     * "timezone_type": 3,
     * "timezone": "UTC"
     * },
     * "arrivalDateTime": {
     * "date": "2018-09-20 04:30:00.000000",
     * "timezone_type": 3,
     * "timezone": "UTC"
     * },
     * "flightNumber": "126",
     * "bookingClass": "O",
     * "departureCity": "Boston",
     * "arrivalCity": "Reykjavik",
     * "flightDuration": 330,
     * "layoverDuration": 0,
     * "cabin": "E",
     * "departureCountry": "United States",
     * "arrivalCountry": "Iceland"
     * },
     * {
     * "carrier": "WW",
     * "airlineName": "WOW air",
     * "departureAirport": "KEF",
     * "arrivalAirport": "LGW",
     * "departureDateTime": {
     * "date": "2018-09-20 15:30:00.000000",
     * "timezone_type": 3,
     * "timezone": "UTC"
     * },
     * "arrivalDateTime": {
     * "date": "2018-09-20 19:50:00.000000",
     * "timezone_type": 3,
     * "timezone": "UTC"
     * },
     * "flightNumber": "814",
     * "bookingClass": "N",
     * "departureCity": "Reykjavik",
     * "arrivalCity": "London",
     * "flightDuration": 200,
     * "layoverDuration": 660,
     * "cabin": "E",
     * "departureCountry": "Iceland",
     * "arrivalCountry": "United Kingdom"
     * }
     * ],
     * "totalDuration": 1190,
     * "routing": "BOS-KEF-LGW",
     * "title": "Boston - London"
     * }
     * ],
     * "price": {
     * "detail": {
     * "ADT": {
     * "selling": 350.2,
     * "fare": 237,
     * "taxes": 113.2,
     * "tickets": 1
     * }
     * },
     * "tickets": 1,
     * "selling": 350.2,
     * "amountPerPax": 350.2,
     * "fare": 237,
     * "mark_up": 0,
     * "taxes": 113.2,
     * "currency": "USD",
     * "isCC": false
     * }
     * },
     * "errors": [],
     * "uid": "5b7424e858e91",
     * "lead_id": 123456,
     * "lead_uid": "00jhk0017"
     * "agentName": "admin",
     * "agentEmail": "assistant@wowfare.com",
     * "agentDirectLine": "+1 888 946 3882",
     * "action": "v1/quote/get-info",
     * "response_id": 173,
     * "request_dt": "2018-08-16 06:42:03",
     * "response_dt": "2018-08-16 06:42:03"
     * }
     *
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


    public function actionGetInfo(): array
    {

        $this->checkPost();
        $apiLog = $this->startApiLog($this->action->uniqueId);


        $uid = Yii::$app->request->post('uid');

        if (!$uid) {
            throw new BadRequestHttpException('Not found UID on POST request', 1);
        }

        $model = Quote::find()->where(['uid' => $uid])->one();

        if (!$model) {
            throw new NotFoundHttpException('Not found Quote UID: ' . $uid, 2);
        }


        $response = [
            'status' => 'Failed',
            'itinerary' => [],
            'errors' => []
        ];

        try {

            $response['status'] = ($model->status != $model::STATUS_DECLINED) ? 'Success' : 'Failed';

            $sellerContactInfo = EmployeeContactInfo::findOne([
                'employee_id' => $model->lead->employee_id,
                'project_id' => $model->lead->project_id
            ]);

            $response['uid'] = $uid;
            $response['lead_id'] = $model->lead->id;
            $response['lead_uid'] = $model->lead->uid;

            $response['agentName'] = $model->lead->employee->username;
            $response['agentEmail'] = $sellerContactInfo ? $sellerContactInfo->email_user : $model->lead->project->contactInfo->email;

            $response['agentDirectLine'] = ($sellerContactInfo !== null) ? $sellerContactInfo->direct_line : sprintf('+1 %s', $model->lead->project->contactInfo->phone);
            $response['itinerary']['tripType'] = $model->trip_type;
            $response['itinerary']['mainCarrier'] = ($model->getMainCarrier()) ? $model->getMainCarrier()->name : $model->main_airline_code;
            $response['itinerary']['trips'] = $model->getTrips();
            $response['itinerary']['price'] = $model->quotePrice();

            if ($model->status == Quote::STATUS_SEND) {
                $model->status = Quote::STATUS_OPENED;
                $model->save();
            }


        } catch (\Throwable $e) {

            Yii::error($e->getTraceAsString(), 'API:Quote:get:try');
            if (Yii::$app->request->get('debug')) $message = ($e->getTraceAsString());
            else $message = $e->getMessage() . ' (code:' . $e->getCode() . ', line: ' . $e->getLine() . ')';

            $response['error'] = $message;
            $response['errors'] = $message;
            $response['error_code'] = 30;
        }


        $responseData = $response;
        $responseData = $apiLog->endApiLog($responseData);

        if (isset($response['error']) && $response['error']) {
            $json = @json_encode($response['error']);
            if (isset($response['error_code']) && $response['error_code']) $error_code = $response['error_code'];
            else $error_code = 0;
            throw new UnprocessableEntityHttpException($json, $error_code);
        }

        return $responseData;
    }

    /**
     * @return mixed
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     * @throws \yii\db\Exception
     */
    public function actionUpdate()
    {
        $this->checkPost();
        $apiLog = $this->startApiLog($this->action->uniqueId);

        $quoteAttributes = Yii::$app->request->post((new Quote())->formName());
        if (empty($quoteAttributes)) {
            throw new BadRequestHttpException((new Quote())->formName() . ' is required', 1);
        }

        $model = Quote::findOne(['uid' => $quoteAttributes['uid']]);
        if (!$model) {
            throw new NotFoundHttpException('Not found Quote UID: ' . $quoteAttributes['uid'], 2);
        }

        $response = [
            'status' => 'Failed',
            'errors' => []
        ];

        if (isset($quoteAttributes['needSync']) && $quoteAttributes['needSync'] == true) {
            $data = $model->lead->getLeadInformationForExpert();
            $result = BackOffice::sendRequest('lead/update-lead', 'POST', json_encode($data));
            if ($result['status'] == 'Success' && empty($result['errors'])) {
                $response['status'] = 'Success';
            } else {
                $response['errors'] = $result['errors'];
            }
        } else {

            $changedAttributes = $model->attributes;
            $changedAttributes['selling'] = $model->quotePrice()['selling'];
            $selling = 0;

            $leadAttributes = Yii::$app->request->post((new Lead())->formName());

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->attributes = $quoteAttributes;
                $model->save();

                $quotePricesAttributes = Yii::$app->request->post((new QuotePrice())->formName());
                if (!empty($quotePricesAttributes)) {
                    foreach ($quotePricesAttributes as $quotePriceAttributes) {
                        $quotePrice = QuotePrice::findOne([
                            'uid' => $quotePriceAttributes['uid']
                        ]);
                        if ($quotePrice) {
                            $quotePrice->attributes = $quotePriceAttributes;
                            $selling += $quotePrice->selling;
                            if (!$quotePrice->save()) {
                                $response['errors'][] = $quotePrice->getErrors();
                            }
                        }
                    }
                }

                if (!$model->hasErrors()) {
                    if (!empty($leadAttributes)) {
                        $model->lead->attributes = $leadAttributes;
                        if (!$model->lead->save()) {
                            $response['errors'][] = $model->lead->getErrors();
                        }
                    }
                    if ($model->status == Quote::STATUS_APPLIED) {
                        $model->lead->status = Lead::STATUS_BOOKED;
                        $model->lead->save(false, ['status']);
                    }
                    $response['status'] = 'Success';
                    $transaction->commit();

                    //Add logs after changed model attributes
                    $leadLog = new LeadLog((new LeadLogMessage()));
                    $leadLog->logMessage->oldParams = $changedAttributes;
                    $newParams = array_intersect_key($model->attributes, $changedAttributes);
                    $newParams['selling'] = round($selling, 2);
                    $leadLog->logMessage->newParams = $newParams;
                    $leadLog->logMessage->title = 'Update';
                    $leadLog->logMessage->model = sprintf('%s (%s)', $model->formName(), $model->uid);
                    $leadLog->addLog([
                        'lead_id' => $model->lead_id,
                    ]);

                } else {
                    $response['errors'][] = $model->getErrors();
                    $transaction->rollBack();
                }
            } catch (\Throwable $e) {

                Yii::error($e->getTraceAsString(), 'API:Quote:update:try');
                if (Yii::$app->request->get('debug')) $message = ($e->getTraceAsString());
                else $message = $e->getMessage() . ' (code:' . $e->getCode() . ', line: ' . $e->getLine() . ')';

                $response['error'] = $message;
                $response['errors'] = $message;
                $response['error_code'] = 30;

                $transaction->rollBack();
            }
        }

        $responseData = $response;
        $responseData = $apiLog->endApiLog($responseData);

        if (isset($response['error']) && $response['error']) {
            $json = @json_encode($response['error']);
            if (isset($response['error_code']) && $response['error_code']) $error_code = $response['error_code'];
            else $error_code = 0;
            throw new UnprocessableEntityHttpException($json, $error_code);
        }

        return $responseData;
    }

    /**
     * @return mixed
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $this->checkPost();
        $apiLog = $this->startApiLog($this->action->uniqueId);

        $quoteAttributes = Yii::$app->request->post((new Quote())->formName());
        if (empty($quoteAttributes)) {
            throw new BadRequestHttpException((new Quote())->formName() . ' is required', 1);
        }

        $leadAttributes = Yii::$app->request->post((new Lead())->formName());
        if (empty($leadAttributes)) {
            throw new BadRequestHttpException((new Lead())->formName() . ' is required', 1);
        }

        $lead = Lead::findOne([
            'uid' => $leadAttributes['uid'],
            'source_id' => $leadAttributes['market_info_id']
        ]);
        if (!$lead) {
            throw new NotFoundHttpException('Not found Lead UID: ' . $leadAttributes['uid'], 2);
        }

        $model = Quote::findOne(['uid' => $quoteAttributes['uid']]);
        if ($model) {
            throw new NotFoundHttpException('Already Exist Quote UID: ' . $quoteAttributes['uid'], 2);
        } else {
            $model = new Quote();
        }

        $selling = 0;
        $changedAttributes = $model->attributes;
        $changedAttributes['selling'] = $selling;


        $response = [
            'status' => 'Failed',
            'errors' => []
        ];

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model->attributes = $quoteAttributes;
            $model->lead_id = $lead->id;
            $model->employee_id = null;
            $model->save();

            $quotePricesAttributes = Yii::$app->request->post((new QuotePrice())->formName());
            if (!empty($quotePricesAttributes)) {
                foreach ($quotePricesAttributes as $quotePriceAttributes) {
                    $quotePrice = new QuotePrice();
                    if ($quotePrice) {
                        $quotePrice->attributes = $quotePriceAttributes;
                        $quotePrice->quote_id = $model->id;
                        $selling += $quotePrice->selling;
                        if (!$quotePrice->save()) {
                            $response['errors'][] = $quotePrice->getErrors();
                        }
                    }
                }
            }

            if (!$model->hasErrors()) {
                $response['status'] = 'Success';
                $transaction->commit();

                //Add logs after changed model attributes
                $leadLog = new LeadLog((new LeadLogMessage()));
                $leadLog->logMessage->oldParams = $changedAttributes;
                $newParams = array_intersect_key($model->attributes, $changedAttributes);
                $newParams['selling'] = round($selling, 2);
                $leadLog->logMessage->newParams = $newParams;
                $leadLog->logMessage->title = 'Create';
                $leadLog->logMessage->model = sprintf('%s (%s)', $model->formName(), $model->uid);
                $leadLog->addLog([
                    'lead_id' => $model->lead_id,
                ]);

            } else {
                $response['errors'][] = $model->getErrors();
                $transaction->rollBack();
            }
        } catch (\Throwable $e) {

            Yii::error($e->getTraceAsString(), 'API:Quote:create:try');
            if (Yii::$app->request->get('debug')) $message = ($e->getTraceAsString());
            else $message = $e->getMessage() . ' (code:' . $e->getCode() . ', line: ' . $e->getLine() . ')';

            $response['error'] = $message;
            $response['errors'] = $message;
            $response['error_code'] = 30;

            $transaction->rollBack();
        }

        $responseData = $response;
        $responseData = $apiLog->endApiLog($responseData);

        if (isset($response['error']) && $response['error']) {
            $json = @json_encode($response['error']);
            if (isset($response['error_code']) && $response['error_code']) $error_code = $response['error_code'];
            else $error_code = 0;
            throw new UnprocessableEntityHttpException($json, $error_code);
        }

        return $responseData;
    }
}

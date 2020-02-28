<?php

namespace webapi\modules\v1\controllers;

use common\components\BackOffice;
use common\models\EmployeeContactInfo;
use common\models\GlobalLog;
use common\models\Lead;
//use common\models\LeadLog;
use common\models\local\LeadLogMessage;
use common\models\Notifications;
use common\models\Quote;
use common\models\QuotePrice;
use common\models\UserProjectParams;
use modules\lead\src\entities\lead\LeadQuery;
use sales\auth\Auth;
use sales\logger\db\GlobalLogInterface;
use sales\logger\db\LogDTO;
use sales\repositories\lead\LeadRepository;
use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;
use common\models\QuoteSegment;
use common\models\QuoteTrip;
use common\models\QuoteSegmentBaggage;
use common\models\QuoteSegmentBaggageCharge;


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
     * @apiParam {string{13}}       uid                 Quote UID
     * @apiParam {string}           [apiKey]            API Key for Project (if not use Basic-Authorization)
     * @apiParam {string}           [clientIP]          Client IP address
     * @apiParam {bool}             [clientUseProxy]    Client Use Proxy
     * @apiParam {string}           [clientUserAgent]   Client User Agent
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
     * "lead_uid": "00jhk0017",
     * "client_id": 1034,
     * "lead_delayed_charge": 0,
     * "lead_status": "sold",
     * "booked_quote_uid": "5b8ddfc56a15c",
     * "source_code":"38T556",
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
        $clientIP = Yii::$app->request->post('clientIP');

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

            /*$sellerContactInfo = EmployeeContactInfo::findOne([
                'employee_id' => $model->lead->employee_id,
                'project_id' => $model->lead->project_id
            ]);*/

            $userProjectParams = UserProjectParams::findOne([
                'upp_user_id' => $model->lead->employee_id,
                'upp_project_id' => $model->lead->project_id
            ]);

            $response['uid'] = $uid;
            $response['lead_id'] = $model->lead->id;
            $response['lead_uid'] = $model->lead->uid;
            $response['client_id'] = $model->lead->client_id;
            $response['lead_delayed_charge'] = $model->lead->l_delayed_charge;
            $response['lead_status'] = null;
            $response['booked_quote_uid'] = null;
            $response['source_code'] = ($model->lead && isset($model->lead->source)) ? $model->lead->source->cid : null;
            $response['gdsOfferId'] = $model->gds_offer_id;

            if(in_array($model->lead->status,[10,12])){
                $response['lead_status'] = ($model->lead->status == 10)?'sold':'booked';
                $response['booked_quote_uid'] = $model->lead->getBookedQuoteUid();
            }

            $response['agentName'] = $model->lead->employee ? $model->lead->employee->username : '';
            $response['agentEmail'] = $userProjectParams ? $userProjectParams->upp_email : $model->lead->project->contactInfo->email;
            $response['agentDirectLine'] = $userProjectParams ? $userProjectParams->upp_tw_phone_number : sprintf('%s', $model->lead->project->contactInfo->phone);
            $response['generalEmail'] = $model->lead->project->contactInfo->email;
            $response['generalDirectLine'] = sprintf('%s', $model->lead->project->contactInfo->phone);
            $response['itinerary']['tripType'] = $model->trip_type;
            $response['itinerary']['mainCarrier'] = $model->mainAirline ? $model->mainAirline->name : $model->main_airline_code;
            $response['itinerary']['trips'] = $model->getTrips();
            $response['itinerary']['price'] = $model->getQuotePriceData(); //$model->quotePrice();

            if ((int)$model->status === Quote::STATUS_SEND) {
                $excludeIP = Quote::isExcludedIP($clientIP);
                if (!$excludeIP) {
                    $model->status = Quote::STATUS_OPENED;
                    if ($model->save()) {
                        $host = Yii::$app->params['url_address'];
                        $lead = $model->lead;
                        if ($lead) {
                            $project_name = $lead->project ? $lead->project->name : '';
                            $subject = 'Quote- ' . $model->uid . ' OPENED';
                            $message = 'Your Quote (UID: ' . $model->uid . ") has been OPENED by client! \r\nProject: " . Html::encode($project_name) . "! \r\nlead: " . $host . '/lead/view/' . $lead->gid;

                            if ($lead->employee_id) {
                                $isSend = Notifications::create($lead->employee_id, $subject, $message, Notifications::TYPE_INFO, true);
                                if ($isSend) {
                                    // Notifications::socket($lead->employee_id, null, 'getNewNotification', [], true);
                                    Notifications::sendSocket('getNewNotification', ['user_id' => $lead->employee_id]);
                                }
                            }
                        }
                    }
                    //exec(dirname(Yii::getAlias('@app')) . '/yii quote/send-opened-notification '.$uid.'  > /dev/null 2>&1 &');
                }
            } elseif ($model->isDeclined()) {
                if ($lead = $model->lead) {
                    $project_name = $lead->project ? $lead->project->name : '';
                    $subject = 'Quote- ' . $model->uid . ' OPENED';
                    $host = Yii::$app->params['url_address'];
                    $message = 'Your Declined Quote (UID: ' . $model->uid . ") has been OPENED by client! \r\nProject: " . Html::encode($project_name) . "! \r\nlead: " . $host . '/lead/view/' . $lead->gid;

                    if ($lead->employee_id) {
                        $isSend = Notifications::create($lead->employee_id, $subject, $message, Notifications::TYPE_INFO, true);
                        if ($isSend) {
                            // Notifications::socket($lead->employee_id, null, 'getNewNotification', [], true);
                            Notifications::sendSocket('getNewNotification', ['user_id' => $lead->employee_id]);
                        }
                    }
                }
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

        $oldQuoteType = $model->type_id;

        $response = [
            'status' => 'Failed',
            'errors' => []
        ];

        if(isset($quoteAttributes['baggage']) && !empty($quoteAttributes['baggage'])){
            foreach ($quoteAttributes['baggage'] as $baggageAttr){
                $segmentKey = $baggageAttr['segment'];
                $origin = substr($segmentKey, 0, 3);
                $destination = substr($segmentKey, 2, 3);
                $segments = QuoteSegment::find()->innerJoin(QuoteTrip::tableName(),'qs_trip_id = qt_id')
                ->andWhere(['qt_quote_id' =>  $model->id])
                ->andWhere(['or',
                    ['qs_departure_airport_code'=>$origin],
                    ['qs_arrival_airport_code'=>$destination]
                ])
                ->all();
                if(!empty($segments)){
                    $segmentsIds = [];
                    foreach ($segments as $segment){
                        $segmentsIds[] = $segment->qs_id;
                    }
                    if(isset($baggageAttr['free_baggage']) && isset($baggageAttr['free_baggage']['piece'])){
                        //QuoteSegmentBaggage::deleteAll('qsb_segment_id IN (:segments)',[':segments' => implode(',', $segmentsIds)]);

                        if ($segmentsIds) {
                            QuoteSegmentBaggage::deleteAll(['qsb_segment_id' => $segmentsIds]);
                        }

                        foreach ($segments as $segment){
                            $baggage = new QuoteSegmentBaggage();
                            $baggage->qsb_allow_pieces = $baggageAttr['free_baggage']['piece'];
                            $baggage->qsb_segment_id = $segment->qs_id;
                            if(isset($baggageAttr['free_baggage']['weight'])){
                                $baggage->qsb_allow_max_weight = substr($baggageAttr['free_baggage']['weight'],0,100);
                            }
                            if(isset($baggageAttr['free_baggage']['height'])){
                                $baggage->qsb_allow_max_size = substr($baggageAttr['free_baggage']['height'],0,100);
                            }
                            $baggage->save(false);
                        }
                    }
                    if(isset($baggageAttr['paid_baggage']) && !empty($baggageAttr['paid_baggage'])){
                        //QuoteSegmentBaggageCharge::deleteAll('qsbc_segment_id IN (:segments)',[':segments' => implode(',', $segmentsIds)]);
                        if ($segmentsIds) {
                            QuoteSegmentBaggageCharge::deleteAll(['qsbc_segment_id' => $segmentsIds]);
                        }
                        foreach ($segments as $segment){
                            foreach ($baggageAttr['paid_baggage'] as $paidBaggageAttr){
                                $baggage = new QuoteSegmentBaggageCharge();
                                $baggage->qsbc_segment_id = $segment->qs_id;
                                $baggage->qsbc_price = str_replace('USD', '', $paidBaggageAttr['price']);
                                if(isset($paidBaggageAttr['piece'])){
                                    $baggage->qsbc_first_piece = $paidBaggageAttr['piece'];
                                    $baggage->qsbc_last_piece = $paidBaggageAttr['piece'];
                                }
                                if(isset($paidBaggageAttr['weight'])){
                                    $baggage->qsbc_max_weight = substr($paidBaggageAttr['weight'], 0 , 100);
                                }
                                if(isset($paidBaggageAttr['height'])){
                                    $baggage->qsbc_max_size = substr($paidBaggageAttr['height'],0, 100);
                                }
                                $baggage->save(false);
                            }
                        }
                    }
                }
            }
        }

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
            $changedAttributes['selling'] = $model->getPricesData()['total']['selling'];
            //$selling = 0;

            $leadAttributes = Yii::$app->request->post((new Lead())->formName());

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->attributes = $quoteAttributes;

                $type = $quoteAttributes['type_id'] ?? null;
                if ($lead = $model->lead) {
                    $this->setTypeQuoteUpdate($type, $model, $lead);
                } else {
                    $model->type_id = $oldQuoteType;
                    Yii::error('Not found Lead for Quote Id: ' . $model->id, 'API:Quote:update:Lead Not found');
                }

                $model->save();

                $quotePricesAttributes = Yii::$app->request->post((new QuotePrice())->formName());
                if (!empty($quotePricesAttributes)) {
                    foreach ($quotePricesAttributes as $quotePriceAttributes) {
                        $quotePrice = QuotePrice::findOne([
                            'uid' => $quotePriceAttributes['uid']
                        ]);
                        if ($quotePrice) {
                            $quotePrice->attributes = $quotePriceAttributes;
                            //$selling += $quotePrice->selling;
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
                        if (!$model->lead->isBooked()) {
                            try {
                                $repo = Yii::createObject(LeadRepository::class);
                                $newOwner = $model->lead->employee_id;
                                if (!$newOwner) {
                                    $newOwner = LeadQuery::getLastActiveUserId($model->lead->id);
                                }
                                $model->lead->booked($newOwner, null);
                                $repo->save($model->lead);
                            } catch (\Throwable $e) {
                                Yii::error($e->getMessage(), 'API:Quote:Lead:Booked');
                            }
                        }
//                        $model->lead->status = Lead::STATUS_BOOKED;
//                        $model->lead->save(false, ['status']);
                    }
                    $response['status'] = 'Success';
                    $transaction->commit();

                    //Add logs after changed model attributes

                    //todo
//                    $leadLog = new LeadLog((new LeadLogMessage()));
//                    $leadLog->logMessage->oldParams = $changedAttributes;
//                    $newParams = array_intersect_key($model->attributes, $changedAttributes);
//                    $newParams['selling'] = round($model->getPricesData()['total']['selling'], 2);
//                    $leadLog->logMessage->newParams = $newParams;
//                    $leadLog->logMessage->title = 'Update';
//                    $leadLog->logMessage->model = sprintf('%s (%s)', $model->formName(), $model->uid);
//                    $leadLog->addLog([
//                        'lead_id' => $model->lead_id,
//                    ]);

                    (\Yii::createObject(GlobalLogInterface::class))->log(
                        new LogDTO(
                            get_class($model),
                            $model->id,
                            \Yii::$app->id,
                            null,
                            Json::encode(['selling' => $changedAttributes['selling'] ?? 0]),
                            Json::encode(['selling' => round($model->getPricesData()['total']['selling'], 2)]),
                            null,
                            GlobalLog::ACTION_TYPE_UPDATE
                        )
                    );

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
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSync(): array
    {
        $this->checkPost();
        $apiLog = $this->startApiLog($this->action->uniqueId);

        $quoteAttributes = Yii::$app->request->post((new Quote())->formName());

        if (empty($quoteAttributes)) {
            throw new BadRequestHttpException((new Quote())->formName() . ' is required', 1);
        }

        if(!isset($quoteAttributes['uid'])) {
            throw new BadRequestHttpException('Not found uid in POST request', 2);
        }

        $quoteUid = $quoteAttributes['uid'];

        $model = Quote::findOne(['uid' => $quoteUid]);
        if (!$model) {
            throw new NotFoundHttpException('Not found Quote UID: ' . $quoteUid, 3);
        }

        if (!$model->lead) {
            throw new NotFoundHttpException('Not found Lead, Quote UID: ' . $quoteUid, 4);
        }

        $response = [
            'status' => 'Failed',
            'errors' => []
        ];

        $data = $model->lead->getLeadInformationForExpert();

        if($data) {
            $result = BackOffice::sendRequest('lead/update-lead', 'POST', json_encode($data));
            if ($result['status'] === 'Success' && empty($result['errors'])) {
                $response['status'] = 'Success';
            } else {
                $response['errors'] = $result['errors'];
                $response['error_code'] = 10;
            }
        } else {
            throw new NotFoundHttpException('Not found Lead Information, Quote UID: ' . $quoteUid, 5);
        }

        $responseData = $response;
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

            $type = $quoteAttributes['type_id'] ?? null;
            $this->setTypeQuoteInsert($type, $model, $lead);

            $model->save();
            $model->createQuoteTrips();

            if(isset($quoteAttributes['baggage']) && !empty($quoteAttributes['baggage'])){
                foreach ($quoteAttributes['baggage'] as $baggageAttr){
                    $segmentKey = $baggageAttr['segment'];
                    $origin = substr($segmentKey, 0, 3);
                    $destination = substr($segmentKey, 2, 3);
                    $segment = QuoteSegment::find()->innerJoin(QuoteTrip::tableName(),'qs_trip_id = qt_id')
                                ->andWhere(['qt_quote_id' =>  $model->id])
                                ->andWhere(['or',
                                    ['qs_departure_airport_code'=>$origin],
                                    ['qs_arrival_airport_code'=>$destination]
                                ])
                                ->one();
                    $segments = [];
                    if(!empty($segment)){
                        $segments = QuoteSegment::find()
                        ->andWhere(['qs_trip_id' =>  $segment->qs_trip_id])
                        ->all();
                    }
                    if(!empty($segments)){
                        if(isset($baggageAttr['free_baggage']) && isset($baggageAttr['free_baggage']['piece'])){
                            foreach ($segments as $segment){
                                $baggage = new QuoteSegmentBaggage();
                                $baggage->qsb_allow_pieces = $baggageAttr['free_baggage']['piece'];
                                $baggage->qsb_segment_id = $segment->qs_id;
                                if(isset($baggageAttr['free_baggage']['weight'])){
                                    $baggage->qsb_allow_max_weight = substr($baggageAttr['free_baggage']['weight'], 0, 100);
                                }
                                if(isset($baggageAttr['free_baggage']['height'])){
                                    $baggage->qsb_allow_max_size = substr($baggageAttr['free_baggage']['height'], 0, 100);
                                }
                                $baggage->save(false);
                            }
                        }
                        if(isset($baggageAttr['paid_baggage']) && !empty($baggageAttr['paid_baggage'])){
                            foreach ($segments as $segment){
                                foreach ($baggageAttr['paid_baggage'] as $paidBaggageAttr){
                                    $baggage = new QuoteSegmentBaggageCharge();
                                    $baggage->qsbc_segment_id = $segment->qs_id;
                                    $baggage->qsbc_price = str_replace('USD', '', $paidBaggageAttr['price']);
                                    if(isset($paidBaggageAttr['piece'])){
                                        $baggage->qsbc_first_piece = $paidBaggageAttr['piece'];
                                        $baggage->qsbc_last_piece = $paidBaggageAttr['piece'];
                                    }
                                    if(isset($paidBaggageAttr['weight'])){
                                        $baggage->qsbc_max_weight = substr($paidBaggageAttr['weight'], 0 , 100);
                                    }
                                    if(isset($paidBaggageAttr['height'])){
                                        $baggage->qsbc_max_size = substr($paidBaggageAttr['height'],0, 100);
                                    }
                                    $baggage->save(false);
                                }
                            }
                        }
                    }
                }
            }

            $quotePricesAttributes = Yii::$app->request->post((new QuotePrice())->formName());
            if (!empty($quotePricesAttributes)) {
                foreach ($quotePricesAttributes as $quotePriceAttributes) {
                    $quotePrice = new QuotePrice();
                    if ($quotePrice) {
                        $quotePrice->attributes = $quotePriceAttributes;
                        $quotePrice->quote_id = $model->id;
                        //$selling += $quotePrice->selling;
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
                //todo
//                $leadLog = new LeadLog((new LeadLogMessage()));
//                $leadLog->logMessage->oldParams = $changedAttributes;
//                $newParams = array_intersect_key($model->attributes, $changedAttributes);
//                $newParams['selling'] = round($model->getPricesData()['total']['selling'], 2);
//                $leadLog->logMessage->newParams = $newParams;
//                $leadLog->logMessage->title = 'Create';
//                $leadLog->logMessage->model = sprintf('%s (%s)', $model->formName(), $model->uid);
//                $leadLog->addLog([
//                    'lead_id' => $model->lead_id,
//                ]);

                (\Yii::createObject(GlobalLogInterface::class))->log(
                    new LogDTO(
                        get_class($model),
                        $model->id,
                        \Yii::$app->id,
                        null,
                        Json::encode(['selling' => $changedAttributes['selling'] ?? 0]),
                        Json::encode(['selling' => round($model->getPricesData()['total']['selling'], 2)]),
                        null,
                        GlobalLog::ACTION_TYPE_UPDATE
                    )
                );

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
            $json = $response['error']; //@json_encode($response['error']);
            if (isset($response['error_code']) && $response['error_code']) {
                $error_code = (int) $response['error_code'];
            } else {
                $error_code = 0;
            }
            throw new UnprocessableEntityHttpException(VarDumper::dumpAsString($json, 10), $error_code);
        }

        return $responseData;
    }

    private function setTypeQuoteInsert($type, Quote $quote, Lead $lead): void
    {
        if ($type !== null) {
            $type = (int)$type;
        }

        $this->setTypeQuote($type, $quote, $lead);
    }

    private function setTypeQuoteUpdate($type, Quote $quote, Lead $lead): void
    {
        if ($type === null) {
            return;
        }

        $type = (int)$type;

        if ($quote->type_id === $type) {
            return;
        }

        $this->setTypeQuote($type, $quote, $lead);
    }

    private function setTypeQuote($type, Quote $quote, Lead $lead): void
    {
        if ($type === Quote::TYPE_ORIGINAL) {
            if ($lead->originalQuoteExist()) {
                throw new \DomainException('Original quote already exist. Lead uid: ' . $lead->uid);
            }
            $quote->original();
            return;
        }

        if ($type === Quote::TYPE_ALTERNATIVE) {
            $quote->alternative();
            return;
        }

        if ($type === Quote::TYPE_BASE) {
            $quote->base();
            return;
        }

        if ($lead->originalQuoteExist()) {
            $quote->alternative();
        } else {
            $quote->base();
        }
    }
}

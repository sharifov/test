<?php

namespace webapi\modules\v1\controllers;

use common\components\BackOffice;
use common\components\purifier\Purifier;
use common\components\SearchService;
use common\models\EmployeeContactInfo;
use common\models\GlobalLog;
use common\models\Lead;
//use common\models\LeadLog;
use common\models\local\LeadLogMessage;
use common\models\Log;
use common\models\Notifications;
use common\models\Quote;
use common\models\QuotePrice;
use common\models\UserProjectParams;
use common\models\VisitorLog;
use frontend\helpers\JsonHelper;
use frontend\widgets\notification\NotificationMessage;
use modules\invoice\src\exceptions\InvoiceCodeException;
use modules\lead\src\entities\lead\LeadQuery;
use sales\auth\Auth;
use sales\helpers\app\AppHelper;
use sales\logger\db\GlobalLogInterface;
use sales\logger\db\LogDTO;
use sales\repositories\lead\LeadRepository;
use sales\services\quote\addQuote\TripService;
use Yii;
use yii\helpers\ArrayHelper;
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
     * HTTP/1.1 200 OK
     * {
     *   "status": "Success",
     *   "itinerary": {
     *       "typeId": 2,
     *       "typeName": "Alternative",
     *       "tripType": "OW",
     *       "mainCarrier": "WOW air",
     *       "trips": [
     *           {
     *               "segments": [
     *                   {
     *                       "carrier": "WW",
     *                       "airlineName": "WOW air",
     *                       "departureAirport": "BOS",
     *                       "arrivalAirport": "KEF",
     *                       "departureDateTime": {
     *                           "date": "2018-09-19 19:00:00.000000",
     *                           "timezone_type": 3,
     *                           "timezone": "UTC"
     *                       },
     *                       "arrivalDateTime": {
     *                           "date": "2018-09-20 04:30:00.000000",
     *                           "timezone_type": 3,
     *                           "timezone": "UTC"
     *                       },
     *                       "flightNumber": "126",
     *                       "bookingClass": "O",
     *                       "departureCity": "Boston",
     *                       "arrivalCity": "Reykjavik",
     *                       "flightDuration": 330,
     *                       "layoverDuration": 0,
     *                       "cabin": "E",
     *                       "departureCountry": "United States",
     *                       "arrivalCountry": "Iceland"
     *                   },
     *                   {
     *                       "carrier": "WW",
     *                       "airlineName": "WOW air",
     *                       "departureAirport": "KEF",
     *                       "arrivalAirport": "LGW",
     *                       "departureDateTime": {
     *                           "date": "2018-09-20 15:30:00.000000",
     *                           "timezone_type": 3,
     *                           "timezone": "UTC"
     *                       },
     *                       "arrivalDateTime": {
     *                           "date": "2018-09-20 19:50:00.000000",
     *                           "timezone_type": 3,
     *                           "timezone": "UTC"
     *                       },
     *                       "flightNumber": "814",
     *                       "bookingClass": "N",
     *                       "departureCity": "Reykjavik",
     *                       "arrivalCity": "London",
     *                       "flightDuration": 200,
     *                       "layoverDuration": 660,
     *                       "cabin": "E",
     *                       "departureCountry": "Iceland",
     *                       "arrivalCountry": "United Kingdom"
     *                   }
     *               ],
     *               "totalDuration": 1190,
     *               "routing": "BOS-KEF-LGW",
     *               "title": "Boston - London"
     *           }
     *       ],
     *       "price": {
     *           "detail": {
     *               "ADT": {
     *                   "selling": 350.2,
     *                   "fare": 237,
     *                   "taxes": 113.2,
     *                   "tickets": 1
     *               }
     *           },
     *           "tickets": 1,
     *           "selling": 350.2,
     *           "amountPerPax": 350.2,
     *           "fare": 237,
     *           "mark_up": 0,
     *           "taxes": 113.2,
     *           "currency": "USD",
     *           "isCC": false
     *       }
     *   },
     *  "itineraryOrigin": {
     *      "uid": "5f207ec202212",
     *      "typeId": 1,
     *      "typeName": "Original",
     *      "tripType": "OW",
     *      "mainCarrier": "WOW air",
     *      "trips": [
     *           {
     *               "segments": [
     *                   {
     *                       "carrier": "WW",
     *                       "airlineName": "WOW air",
     *                       "departureAirport": "BOS",
     *                       "arrivalAirport": "KEF",
     *                       "departureDateTime": {
     *                           "date": "2018-09-19 19:00:00.000000",
     *                           "timezone_type": 3,
     *                           "timezone": "UTC"
     *                       },
     *                       "arrivalDateTime": {
     *                           "date": "2018-09-20 04:30:00.000000",
     *                           "timezone_type": 3,
     *                           "timezone": "UTC"
     *                       },
     *                       "flightNumber": "126",
     *                       "bookingClass": "O",
     *                       "departureCity": "Boston",
     *                       "arrivalCity": "Reykjavik",
     *                       "flightDuration": 330,
     *                       "layoverDuration": 0,
     *                       "cabin": "E",
     *                       "departureCountry": "United States",
     *                       "arrivalCountry": "Iceland"
     *                   }
     *               ],
     *               "totalDuration": 1190,
     *               "routing": "BOS-KEF",
     *               "title": "Boston - London"
     *           }
     *       ],
     *       "price": {
     *           "detail": {
     *               "ADT": {
     *                   "selling": 350.2,
     *                   "fare": 237,
     *                   "taxes": 113.2,
     *                   "tickets": 1
     *               }
     *           },
     *           "tickets": 1,
     *           "selling": 350.2,
     *           "amountPerPax": 350.2,
     *           "fare": 237,
     *           "mark_up": 0,
     *           "taxes": 113.2,
     *           "currency": "USD",
     *           "isCC": false
     *       }
     *   },
     *   "errors": [],
     *   "uid": "5b7424e858e91",
     *   "lead_id": 123456,
     *   "lead_uid": "00jhk0017",
     *   "client_id": 1034,
     *   "client": {
     *       "id": 1034,
     *       "uuid": "35009a79-1a05-49d7-b876-2b884d0f825b"
     *    },
     *   "lead_delayed_charge": 0,
     *   "lead_status": "sold",
     *   "booked_quote_uid": "5b8ddfc56a15c",
     *   "source_code": "38T556",
     *   "check_payment": true,
     *   "agentName": "admin",
     *   "agentEmail": "assistant@wowfare.com",
     *   "agentDirectLine": "+1 888 946 3882",
     *   "visitor_log": {
     *       "vl_source_cid": "string_abc",
     *       "vl_ga_client_id": "35009a79-1a05-49d7-b876-2b884d0f825b",
     *       "vl_ga_user_id": "35009a79-1a05-49d7-b876-2b884d0f825b",
     *       "vl_customer_id": "3",
     *       "vl_gclid": "gclid=TeSter-123#bookmark",
     *       "vl_dclid": "CJKu8LrQxd4CFQ1qwQodmJIElw",
     *       "vl_utm_source": "newsletter4",
     *       "vl_utm_medium": "string_abc",
     *       "vl_utm_campaign": "string_abc",
     *       "vl_utm_term": "string_abc",
     *       "vl_utm_content": "string_abc",
     *       "vl_referral_url": "string_abc",
     *       "vl_location_url": "string_abc",
     *       "vl_user_agent": "string_abc",
     *       "vl_ip_address": "127.0.0.1",
     *       "vl_visit_dt": "2020-02-14 12:00:00",
     *       "vl_created_dt": "2020-02-28 17:17:33"
     *   },
     *  "lead": {
     *       "additionalInformation": [
     *           {
     *              "pnr": "example_pnr",
     *               "bo_sale_id": "example_sale_id",
     *              "vtf_processed": null,
     *              "tkt_processed": null,
     *              "exp_processed": null,
     *              "passengers": [],
     *              "paxInfo": []
     *          }
     *      ]
     *  },
     *   "action": "v1/quote/get-info",
     *   "response_id": 173,
     *   "request_dt": "2018-08-16 06:42:03",
     *   "response_dt": "2018-08-16 06:42:03"
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

//            $userProjectParams = UserProjectParams::findOne([
//                'upp_user_id' => $model->lead->employee_id,
//                'upp_project_id' => $model->lead->project_id
//            ]);

            $userProjectParams = UserProjectParams::find()
                ->andWhere(['upp_user_id' => $model->lead->employee_id,'upp_project_id' => $model->lead->project_id])
                ->withEmailList()
                ->withPhoneList()
                ->one();

            $response['uid'] = $uid;
            $response['lead_id'] = $model->lead->id;
            $response['lead_uid'] = $model->lead->uid;
            $response['client_id'] = $model->lead->client_id;
            $response['client'] = [
                'id' => $model->lead->client->id,
                'uuid' => $model->lead->client->uuid,
            ];
            $response['lead_delayed_charge'] = $model->lead->l_delayed_charge;
            $response['lead_status'] = null;
            $response['booked_quote_uid'] = null;
            $response['source_code'] = ($model->lead && isset($model->lead->source)) ? $model->lead->source->cid : null;
            $response['gdsOfferId'] = $model->gds_offer_id;
            $response['check_payment'] = (bool) $model->check_payment;

            if (in_array($model->lead->status, [10,12])) {
                $response['lead_status'] = ($model->lead->status == 10) ? 'sold' : 'booked';
                $response['booked_quote_uid'] = $model->lead->getBookedQuoteUid();
            }

            $response['agentName'] = $model->lead->employee ? $model->lead->employee->username : '';
//            $response['agentEmail'] = $userProjectParams ? $userProjectParams->upp_email : $model->lead->project->contactInfo->email;
            $response['agentEmail'] = ($userProjectParams && $userProjectParams->getEmail()) ? $userProjectParams->getEmail() : $model->lead->project->contactInfo->email;
//            $response['agentDirectLine'] = $userProjectParams ? $userProjectParams->upp_tw_phone_number : sprintf('%s', $model->lead->project->contactInfo->phone);
            $response['agentDirectLine'] = ($userProjectParams && $userProjectParams->getPhone()) ? $userProjectParams->getPhone() : sprintf('%s', $model->lead->project->contactInfo->phone);
            $response['generalEmail'] = $model->lead->project->contactInfo->email;
            $response['generalDirectLine'] = sprintf('%s', $model->lead->project->contactInfo->phone);

            $response['itinerary']['typeId'] = $model->type_id;
            $response['itinerary']['typeName'] = Quote::getTypeName($model->type_id);
            $response['itinerary']['tripType'] = $model->trip_type;
            $response['itinerary']['mainCarrier'] = $model->mainAirline ? $model->mainAirline->name : $model->main_airline_code;
            $response['itinerary']['trips'] = $model->getTrips();
            $response['itinerary']['price'] = $model->getQuotePriceData(); //$model->quotePrice();

            if ($model->isAlternative() && $originalQuote = Quote::getOriginalQuoteByLeadId($model->lead_id)) {
                $response['itineraryOrigin']['uid'] = $originalQuote->uid;
                $response['itineraryOrigin']['typeId'] = $originalQuote->type_id;
                $response['itineraryOrigin']['typeName'] = Quote::getTypeName($originalQuote->type_id);
                $response['itineraryOrigin']['tripType'] = $originalQuote->trip_type;
                $response['itineraryOrigin']['mainCarrier'] = $originalQuote->mainAirline->name ?? $originalQuote->main_airline_code;
                $response['itineraryOrigin']['trips'] = $originalQuote->getTrips();
                $response['itineraryOrigin']['price'] = $originalQuote->getQuotePriceData();
            }

            if ($model->lead) {
                ArrayHelper::setValue(
                    $response,
                    'lead.additionalInformation',
                    $model->lead->additional_information ? JsonHelper::decode($model->lead->additional_information) : ''
                );
            }

            if ((int)$model->status === Quote::STATUS_SEND) {
                $excludeIP = Quote::isExcludedIP($clientIP);
                if (!$excludeIP) {
                    $model->status = Quote::STATUS_OPENED;
                    if ($model->save()) {
                        $lead = $model->lead;
                        if ($lead) {
                            $project_name = $lead->project ? $lead->project->name : '';
                            $subject = 'Quote- ' . $model->uid . ' OPENED';
                            $message = 'Your Quote (UID: ' . $model->uid . ") has been OPENED by client! \r\nProject: " . Html::encode($project_name) . "! \r\nLead (Id: " . Purifier::createLeadShortLink($lead) . ")";

                            if ($lead->employee_id) {
                                if ($ntf = Notifications::create($lead->employee_id, $subject, $message, Notifications::TYPE_INFO, true)) {
                                    // Notifications::socket($lead->employee_id, null, 'getNewNotification', [], true);
                                    $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                                    Notifications::publish('getNewNotification', ['user_id' => $lead->employee_id], $dataNotification);
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
                    $message = 'Your Declined Quote (UID: ' . $model->uid . ") has been OPENED by client! \r\nProject: " . Html::encode($project_name) . "! \r\nLead (Id: " . Purifier::createLeadShortLink($lead) . ")";

                    if ($lead->employee_id) {
                        if ($ntf = Notifications::create($lead->employee_id, $subject, $message, Notifications::TYPE_INFO, true)) {
                            // Notifications::socket($lead->employee_id, null, 'getNewNotification', [], true);
                            $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                            Notifications::publish('getNewNotification', ['user_id' => $lead->employee_id], $dataNotification);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            Yii::error($e->getTraceAsString(), 'API:Quote:get:try');
            if (Yii::$app->request->get('debug')) {
                $message = ($e->getTraceAsString());
            } else {
                $message = $e->getMessage() . ' (code:' . $e->getCode() . ', line: ' . $e->getLine() . ')';
            }

            $response['error'] = $message;
            $response['error_code'] = 30;
        }


        $responseData = $response;
        if (($lead = $model->lead) && $lead->l_visitor_log_id) {
//            $responseData['visitor_log'] = VisitorLog::getVisitorLogsByLead($model->lead_id);
            $responseData['visitor_log'] = VisitorLog::getVisitorLog($lead->l_visitor_log_id);
        }
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
    public function actionUpdate()
    {
        $this->checkPost();
        $apiLog = $this->startApiLog($this->action->uniqueId);

        $quoteAttributes = Yii::$app->request->post((new Quote())->formName());
        if (empty($quoteAttributes)) {
            throw new BadRequestHttpException((new Quote())->formName() . ' is required', 1);
        }

        if (empty($quoteAttributes['uid'])) {
            throw new BadRequestHttpException((new Quote())->formName() . '.uid is required', 1);
        }

        $model = Quote::findOne(['uid' => $quoteAttributes['uid']]);

        if (!$model) {
            throw new NotFoundHttpException('Not found Quote UID: ' . $quoteAttributes['uid'], 2);
        }

        $model->setScenario(Quote::SCENARIO_API_UPDATE);

        $oldQuoteType = $model->type_id;

        $response = [
            'status' => 'Failed',
            'errors' => []
        ];

        if (isset($quoteAttributes['baggage']) && !empty($quoteAttributes['baggage'])) {
            foreach ($quoteAttributes['baggage'] as $baggageAttr) {
                $segmentKey = $baggageAttr['segment'];
                $origin = substr($segmentKey, 0, 3);
                $destination = substr($segmentKey, 2, 3);
                $segments = QuoteSegment::find()->innerJoin(QuoteTrip::tableName(), 'qs_trip_id = qt_id')
                ->andWhere(['qt_quote_id' =>  $model->id])
                ->andWhere(['or',
                    ['qs_departure_airport_code' => $origin],
                    ['qs_arrival_airport_code' => $destination]
                ])
                ->all();
                if (!empty($segments)) {
                    $segmentsIds = [];
                    foreach ($segments as $segment) {
                        $segmentsIds[] = $segment->qs_id;
                    }
                    if (isset($baggageAttr['free_baggage']) && isset($baggageAttr['free_baggage']['piece'])) {
                        //QuoteSegmentBaggage::deleteAll('qsb_segment_id IN (:segments)',[':segments' => implode(',', $segmentsIds)]);

                        if ($segmentsIds) {
                            QuoteSegmentBaggage::deleteAll(['qsb_segment_id' => $segmentsIds]);
                        }

                        foreach ($segments as $segment) {
                            $baggage = new QuoteSegmentBaggage();
                            $baggage->qsb_allow_pieces = $baggageAttr['free_baggage']['piece'];
                            $baggage->qsb_segment_id = $segment->qs_id;
                            if (isset($baggageAttr['free_baggage']['weight'])) {
                                $baggage->qsb_allow_max_weight = substr($baggageAttr['free_baggage']['weight'], 0, 100);
                            }
                            if (isset($baggageAttr['free_baggage']['height'])) {
                                $baggage->qsb_allow_max_size = substr($baggageAttr['free_baggage']['height'], 0, 100);
                            }
                            $baggage->save(false);
                        }
                    }
                    if (isset($baggageAttr['paid_baggage']) && !empty($baggageAttr['paid_baggage'])) {
                        //QuoteSegmentBaggageCharge::deleteAll('qsbc_segment_id IN (:segments)',[':segments' => implode(',', $segmentsIds)]);
                        if ($segmentsIds) {
                            QuoteSegmentBaggageCharge::deleteAll(['qsbc_segment_id' => $segmentsIds]);
                        }
                        foreach ($segments as $segment) {
                            foreach ($baggageAttr['paid_baggage'] as $paidBaggageAttr) {
                                $baggage = new QuoteSegmentBaggageCharge();
                                $baggage->qsbc_segment_id = $segment->qs_id;
                                $baggage->qsbc_price = str_replace('USD', '', $paidBaggageAttr['price']);
                                if (isset($paidBaggageAttr['piece'])) {
                                    $baggage->qsbc_first_piece = $paidBaggageAttr['piece'];
                                    $baggage->qsbc_last_piece = $paidBaggageAttr['piece'];
                                }
                                if (isset($paidBaggageAttr['weight'])) {
                                    $baggage->qsbc_max_weight = substr($paidBaggageAttr['weight'], 0, 100);
                                }
                                if (isset($paidBaggageAttr['height'])) {
                                    $baggage->qsbc_max_size = substr($paidBaggageAttr['height'], 0, 100);
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

            if (!array_key_exists('status', $result)) {
                $response['errors'] = 'Not found status key from response (BackOffice - lead/update-lead)';
                Yii::error(
                    [
                        'result' => VarDumper::dumpAsString($result),
                        'data' => $data,
                    ],
                    'QuoteController:actionUpdate:update-lead'
                );
            } elseif ($result['status'] === 'Success' && empty($result['errors'])) {
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
                    $model->lead->sendNotifOnProcessingStatusChanged();

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
                if (Yii::$app->request->get('debug')) {
                    $message = ($e->getTraceAsString());
                } else {
                    $message = $e->getMessage() . ' (code:' . $e->getCode() . ', line: ' . $e->getLine() . ')';
                }

                $response['error'] = $message;
                $response['error_code'] = 30;

                $transaction->rollBack();
            }
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

        if (!isset($quoteAttributes['uid'])) {
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

        if ($data) {
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
     *
     * @api {post} /v1/quote/create Create Quote
     * @apiVersion 0.1.0
     * @apiName CreateQuote
     * @apiGroup Quotes
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string}           [apiKey]                    API Key for Project
     * @apiParam {object}           Lead                        Lead data array
     * @apiParam {string}           [Lead.uid]                  uid
     * @apiParam {int}              [Lead.market_info_id]       market_info_id
     * @apiParam {int}              [Lead.bo_flight_id]         bo_flight_id
     * @apiParam {float}            [Lead.final_profit]         final_profit
     * @apiParam {object}           Quote                       Quote data array
     * @apiParam {string}           [Quote.uid]                 uid
     * @apiParam {string}           [Quote.record_locator]      record_locator
     * @apiParam {string}           [Quote.pcc]                 pcc
     * @apiParam {string}           [Quote.cabin]               cabin
     * @apiParam {string{1}}        Quote.gds                   gds
     * @apiParam {string}           [Quote.trip_type]           trip_type
     * @apiParam {string}           [Quote.main_airline_code]   main_airline_code
     * @apiParam {string}           [Quote.reservation_dump]    reservation_dump
     * @apiParam {int}              [Quote.status]              status
     * @apiParam {string}           [Quote.check_payment]       check_payment
     * @apiParam {string}           [Quote.fare_type]           fare_type
     * @apiParam {string}           [Quote.employee_name]       employee_name
     * @apiParam {bool}             [Quote.created_by_seller]   created_by_seller
     * @apiParam {int}              [Quote.type_id]             type_id
     * @apiParam {object}           QuotePrice[]                QuotePrice data array
     * @apiParam {string}           [QuotePrice.uid]            uid
     * @apiParam {string}           [QuotePrice.passenger_type] passenger_type
     * @apiParam {float}            [QuotePrice.selling]        selling
     * @apiParam {float}            [QuotePrice.net]            net
     * @apiParam {float}            [QuotePrice.fare]           fare
     * @apiParam {float}            [QuotePrice.taxes]          taxes
     * @apiParam {float}            [QuotePrice.mark_up]        mark_up
     * @apiParam {float}            [QuotePrice.extra_mark_up]  extra_mark_up
     * @apiParam {float}            [QuotePrice.service_fee]    service_fee
     *
     * @apiParamExample {json} Request-Example:
     * {
     *      "apiKey": "d190c378e131ccfd8a889c8ee8994cb55f22fbeeb93f9b99007e8e7ecc24d0dd",
     *      "Lead": {
     *          "uid": "5de486f15f095",
     *          "market_info_id": 52,
     *          "bo_flight_id": 0,
     *          "final_profit": 0
     *      },
     *      "Quote": {
     *          "uid": "5f207ec201b99",
     *          "record_locator": null,
     *          "pcc": "0RY9",
     *          "cabin": "E",
     *          "gds": "S",
     *          "trip_type": "RT",
     *          "main_airline_code": "UA",
     *          "reservation_dump": "1 KL6123V 15OCT Q MCOAMS SS1   801P 1100A  16OCT F /DCKL /E \n 2 KL1009L 18OCT S AMSLHR SS1  1015A 1045A /DCKL /E",
     *          "status": 1,
     *          "check_payment": "1",
     *          "fare_type": "TOUR",
     *          "employee_name": "Barry",
     *          "created_by_seller": false,
     *          "type_id" : 0
     *      },
     *      "QuotePrice": [
     *          {
     *              "uid": "expert.5f207ec222c86",
     *              "passenger_type": "ADT",
     *              "selling": 696.19,
     *              "net": 622.65,
     *              "fare": 127,
     *              "taxes": 495.65,
     *              "mark_up": 50,
     *              "extra_mark_up": 0,
     *              "service_fee": 23.54
     *          }
     *      ]
     * }
     *
     * @apiSuccess {string} status    Status
     *
     * @apiSuccess {string} action    Action
     * @apiSuccess {integer} response_id    Response Id
     * @apiSuccess {DateTime} request_dt    Request Date & Time
     * @apiSuccess {DateTime} response_dt   Response Date & Time
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     *  {
     *      "status": "Success",
     *      "action": "v1/quote/create",
     *      "response_id": 11926893,
     *      "request_dt": "2020-09-22 05:05:54",
     *      "response_dt": "2020-09-22 05:05:54",
     *      "execution_time": 0.193,
     *      "memory_usage": 1647440
     *  }
     *
     *
     * @apiErrorExample Error-Response:
     *   HTTP/1.1 404 Not Found
     *   {
     *       "name": "Not Found",
     *       "message": "Already Exist Quote UID: 5f207ec201b19",
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

        $quote = Quote::findOne(['uid' => $quoteAttributes['uid']]);
        if ($quote) {
            throw new NotFoundHttpException('Already Exist Quote UID: ' . $quoteAttributes['uid'], 2);
        }

        $quote = new Quote();
        $selling = 0;
        $changedAttributes = $quote->attributes;
        $changedAttributes['selling'] = $selling;

        $response = [
            'status' => 'Failed',
        ];

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $quote->attributes = $quoteAttributes;
            $quote->lead_id = $lead->id;
            $quote->employee_id = null;
            $quote->setMetricLabels(['action' => 'created', 'type_creation' => 'web_api']);

            $type = $quoteAttributes['type_id'] ?? null;
            $this->setTypeQuoteInsert($type, $quote, $lead);

            $quote->save();
            if ($quote->hasErrors()) {
                throw new \RuntimeException($quote->getErrorSummary(false)[0]);
            }

            if (!ArrayHelper::keyExists($quote->gds, SearchService::GDS_LIST)) {
                $warnings[] = 'Quote GDS (' . $quote->gds . ') not found in GDS_LIST.';
            }

            $tripsSegmentsData = $quote->getTripsSegmentsData();

            $trip = new TripService($quote);
            $trip->createTrips($tripsSegmentsData);
            $quote = $trip->getQuote();

            if (isset($quoteAttributes['baggage']) && !empty($quoteAttributes['baggage'])) {
                foreach ($quoteAttributes['baggage'] as $baggageAttr) {
                    $segmentKey = $baggageAttr['segment'];
                    $origin = substr($segmentKey, 0, 3);
                    $destination = substr($segmentKey, 2, 3);
                    $segment = QuoteSegment::find()->innerJoin(QuoteTrip::tableName(), 'qs_trip_id = qt_id')
                                ->andWhere(['qt_quote_id' =>  $quote->id])
                                ->andWhere(['or',
                                    ['qs_departure_airport_code' => $origin],
                                    ['qs_arrival_airport_code' => $destination]
                                ])
                                ->one();
                    $segments = [];
                    if (!empty($segment)) {
                        $segments = QuoteSegment::find()
                        ->andWhere(['qs_trip_id' =>  $segment->qs_trip_id])
                        ->all();
                    }
                    if (!empty($segments)) {
                        if (isset($baggageAttr['free_baggage']) && isset($baggageAttr['free_baggage']['piece'])) {
                            foreach ($segments as $segment) {
                                $baggage = new QuoteSegmentBaggage();
                                $baggage->qsb_allow_pieces = $baggageAttr['free_baggage']['piece'];
                                $baggage->qsb_segment_id = $segment->qs_id;
                                if (isset($baggageAttr['free_baggage']['weight'])) {
                                    $baggage->qsb_allow_max_weight = substr($baggageAttr['free_baggage']['weight'], 0, 100);
                                }
                                if (isset($baggageAttr['free_baggage']['height'])) {
                                    $baggage->qsb_allow_max_size = substr($baggageAttr['free_baggage']['height'], 0, 100);
                                }
                                $baggage->save(false);
                            }
                        }
                        if (isset($baggageAttr['paid_baggage']) && !empty($baggageAttr['paid_baggage'])) {
                            foreach ($segments as $segment) {
                                foreach ($baggageAttr['paid_baggage'] as $paidBaggageAttr) {
                                    $baggage = new QuoteSegmentBaggageCharge();
                                    $baggage->qsbc_segment_id = $segment->qs_id;
                                    $baggage->qsbc_price = str_replace('USD', '', $paidBaggageAttr['price']);
                                    if (isset($paidBaggageAttr['piece'])) {
                                        $baggage->qsbc_first_piece = $paidBaggageAttr['piece'];
                                        $baggage->qsbc_last_piece = $paidBaggageAttr['piece'];
                                    }
                                    if (isset($paidBaggageAttr['weight'])) {
                                        $baggage->qsbc_max_weight = substr($paidBaggageAttr['weight'], 0, 100);
                                    }
                                    if (isset($paidBaggageAttr['height'])) {
                                        $baggage->qsbc_max_size = substr($paidBaggageAttr['height'], 0, 100);
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
                        $quotePrice->quote_id = $quote->id;
                        if (!$quotePrice->save()) {
                            $warnings[] = $quotePrice->getErrorSummary(false)[0];
                        }
                    }
                }
            }

            if (!$quote->hasErrors()) {
                $response['status'] = 'Success';
                $transaction->commit();

                (\Yii::createObject(GlobalLogInterface::class))->log(
                    new LogDTO(
                        get_class($quote),
                        $quote->id,
                        \Yii::$app->id,
                        null,
                        Json::encode(['selling' => $changedAttributes['selling'] ?? 0]),
                        Json::encode(['selling' => round($quote->getPricesData()['total']['selling'], 2)]),
                        null,
                        GlobalLog::ACTION_TYPE_UPDATE
                    )
                );
            }
        } catch (\Throwable $throwable) {
            $transaction->rollBack();

            if ($throwable->getCode() < 0) {
                Yii::warning(AppHelper::throwableFormatter($throwable), 'API:Quote:create:warning:try');
            } else {
                Yii::error(VarDumper::dumpAsString($throwable), 'API:Quote:create:try');
            }

            if (Yii::$app->request->get('debug')) {
                $message = $throwable->getTraceAsString();
            } else {
                $message = Log::cutErrorMessage($throwable->getMessage());
            }

            $response['error'] = $message;
            $response['error_code'] = 30;
        }

        $responseData = $response;

        if (!empty($warnings)) {
            $responseData['warnings'] = $warnings;
            Yii::warning(VarDumper::dumpAsString($warnings), 'API:Quote:create:warnings');
        }

        $responseData = $apiLog->endApiLog($responseData);

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

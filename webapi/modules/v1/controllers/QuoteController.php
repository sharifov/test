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
use common\models\Project;
use common\models\Quote;
use common\models\QuoteCommunicationOpenLog;
use common\models\QuotePrice;
use common\models\UserProjectParams;
use common\models\VisitorLog;
use frontend\helpers\JsonHelper;
use frontend\helpers\QuoteHelper;
use frontend\widgets\notification\NotificationMessage;
use modules\invoice\src\exceptions\InvoiceCodeException;
use modules\lead\src\entities\lead\LeadQuery;
use src\auth\Auth;
use src\exception\AdditionalDataException;
use src\forms\quote\QuoteCreateDataForm;
use src\forms\quote\QuoteCreateKeyForm;
use src\helpers\app\AppHelper;
use src\logger\db\GlobalLogInterface;
use src\logger\db\LogDTO;
use src\model\leadData\services\LeadDataService;
use src\model\leadPoorProcessing\service\LeadPoorProcessingService;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataDictionary;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogStatus;
use src\model\project\entity\projectRelation\ProjectRelation;
use src\model\project\entity\projectRelation\ProjectRelationQuery;
use src\model\project\entity\projectRelation\ProjectRelationRepository;
use src\model\quoteLabel\service\QuoteLabelService;
use src\repositories\lead\LeadRepository;
use src\repositories\NotFoundException;
use src\repositories\project\ProjectRepository;
use src\services\quote\addQuote\AddQuoteService;
use src\services\quote\addQuote\price\QuotePriceCreateService;
use src\services\quote\addQuote\TripService;
use src\services\quote\quotePriceService\ClientQuotePriceService;
use webapi\src\ApiCodeException;
use webapi\src\behaviors\ApiUserProjectRelatedAccessBehavior;
use webapi\src\Messages;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\DataMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\Message;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\SuccessResponse;
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

/**
 * Class QuoteController
 * @package webapi\modules\v1\controllers
 *
 * @property-read AddQuoteService $addQuoteService
 * @property-read LeadRepository $leadRepository
 * @property-read ProjectRepository $projectRepository
 * @property-read ProjectRelationRepository $projectRelationRepository
 */
class QuoteController extends ApiBaseController
{
    public AddQuoteService $addQuoteService;
    public LeadRepository $leadRepository;
    public ProjectRepository $projectRepository;
    public ProjectRelationRepository $projectRelationRepository;

    public function __construct(
        $id,
        $module,
        AddQuoteService $addQuoteService,
        LeadRepository $leadRepository,
        ProjectRepository $projectRepository,
        ProjectRelationRepository $projectRelationRepository,
        $config = []
    ) {
        $this->addQuoteService = $addQuoteService;
        $this->leadRepository = $leadRepository;
        $this->projectRepository = $projectRepository;
        $this->projectRelationRepository = $projectRelationRepository;
        parent::__construct($id, $module, $config);
    }

    /**
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
     * @apiParam {object}           [queryParams]       Query params, sent to service that calling get-info
     *
     *
     * @apiParamExample {json} Request-Example:
     * {
     *      "uid": "5b6d03d61f078",
     *      "queryParams": {
     *          "qc": "sk2N5"
     *      },
     *      "apiKey": "d190c378e131ccfd8a889c8ee8994cb55f22fbeeb93f9b99007e8e7ecc24d0dd"
     * }
     *
     * @apiSuccess {string} status    Status
     * @apiSuccess {object} itinerary Itinerary List
     *
     * @apiSuccess {array} errors    Errors
     * @apiSuccess {string} uid    Quote UID
     * @apiSuccess {integer} lead_id    Lead ID
     * @apiSuccess {string} lead_uid    Lead UID
     * @apiSuccess {integer} client_id    Client ID
     * @apiSuccess {integer} lead_type    <code>TYPE_ALTERNATIVE = 2, TYPE_FAILED_BOOK = 3</code>
     * @apiSuccess {string} agentName    Agent Name
     * @apiSuccess {string} agentEmail    Agent Email
     * @apiSuccess {string} agentDirectLine    Agent DirectLine
     * @apiSuccess {string} action    Action
     * @apiSuccess {integer} response_id    Response Id
     * @apiSuccess {DateTime} request_dt    Request Date & Time
     * @apiSuccess {DateTime} response_dt   Response Date & Time
     * @apiSuccess {object}     [lead]                          Lead
     * @apiSuccess {string}     [lead.department_key]           Department key (For example: <code>sales,exchange,support,schedule_change,fraud_prevention,chat</code>)
     * @apiSuccess {integer}    [lead.type_create_id]           Type create id
     * @apiSuccess {string}     [lead.type_create_name]         Type Name
     * @apiSuccess {object}     [lead.lead_data]                Lead data
     * @apiSuccess {object}     [lead.additionalInformation]    Additional Information
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
     *   "lead_type": 2,
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
     *              "bo_sale_id": "example_sale_id",
     *              "vtf_processed": null,
     *              "tkt_processed": null,
     *              "exp_processed": null,
     *              "passengers": [],
     *              "paxInfo": []
     *          }
     *      ],
     *      "lead_data": [
     *          {
     *              "ld_field_key": "cross_system_xp",
     *              "ld_field_value": "wpl5.0"
     *          },
     *          {
     *              "ld_field_key": "cross_system_xp",
     *              "ld_field_value": "wpl6.2"
     *          }
     *      ],
     *      "department_key": "chat",
     *      "type_create_id": 8,
     *      "type_create_name": "Client Chat"
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
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionGetInfo(): array
    {
        $this->checkPost();
        $this->startApiLog($this->action->uniqueId);

        $uid = Yii::$app->request->post('uid');
        $clientIP = Yii::$app->request->post('clientIP');

        if (!$uid) {
            throw new BadRequestHttpException('Not found UID on POST request', 1);
        }

        QuoteCommunicationOpenLog::createByRequestData(Yii::$app->getRequest()->getBodyParams());

        if ($this->apiProject) {
            $projectIds = [$this->apiProject->id];
            if ($this->apiProject->projectMainRelation) {
                $projectIds = ArrayHelper::merge($projectIds, [$this->apiProject->projectMainRelation->prl_project_id]);
            }

            if (!$model = Quote::getQuoteByUidAndProjects($uid, $projectIds)) {
                throw new NotFoundHttpException('Not found Quote UID (' . $uid . ') ProjectIds (' . implode(',', $projectIds) . ')', 2);
            }
        } elseif (!$model = Quote::find()->where(['uid' => $uid])->one()) {
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
            $response['lead_type'] = $model->lead->l_type;
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
                $response['lead']['department_key'] = $model->lead->lDep->dep_key ?? null;
                $response['lead']['type_create_id'] = $model->lead->l_type_create ?? null;
                $response['lead']['type_create_name'] = $model->lead->getTypeCreateName();

                ArrayHelper::setValue(
                    $response,
                    'lead.additionalInformation',
                    $model->lead->additional_information ? JsonHelper::decode($model->lead->additional_information) : ''
                );
                ArrayHelper::setValue(
                    $response,
                    'lead.lead_data',
                    LeadDataService::getByLeadForApi($model->lead)
                );
            }

            if ((int)$model->status === Quote::STATUS_SENT) {
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
        $responseData = $this->apiLog->endApiLog($responseData);

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

    //apiDoc was missing and recreated briefly todo double check carefully
    /**
     * @api {post} /v1/quote/sync Sync Quote With BO
     * @apiVersion 0.1.0
     * @apiName SyncQuote
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
     *
     *
     * @apiParamExample {json} Request-Example:
     * {
     *      "uid": "5b6d03d61f078",
     *      "queryParams": {
     *          "qc": "sk2N5"
     *      },
     *      "apiKey": "d190c378e131ccfd8a889c8ee8994cb55f22fbeeb93f9b99007e8e7ecc24d0dd"
     * }
     *
     * @apiSuccess {string} status    Status
     * @apiSuccess {array} errors    Errors
     * @apiSuccess {string} action    Action
     * @apiSuccess {integer} response_id    Response Id
     * @apiSuccess {DateTime} request_dt    Request Date & Time
     * @apiSuccess {DateTime} response_dt   Response Date & Time
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *   "status": "Success",     *
     *   "errors": [],     *
     *   "action": "v1/quote/sync",
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
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSync(): array
    {
        $this->checkPost();
        $this->startApiLog($this->action->uniqueId);

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

        $responseData = $this->apiLog->endApiLog($response);

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
     * @apiParam {object}           [Quote.prod_types[]]        Quote labels
     * @apiParam {string{3}}        [Quote.currency_code]       Currency code
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
     *          "type_id" : 0,
     *          "prod_types" : ["SEP", "TOUR"],
     *          "currency_code" : "USD"
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
        $this->startApiLog($this->action->uniqueId);

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
            $quote->q_create_type_id = Quote::CREATE_TYPE_EXPERT;
            $quote->setMetricLabels(['action' => 'created', 'type_creation' => 'web_api']);

            if ($checkPayment = ArrayHelper::getValue($quote, 'check_payment', true)) {
                $quote->changeServiceFeePercent($quote->serviceFeePercent);
            }

            $type = $quoteAttributes['type_id'] ?? null;
            $this->setTypeQuoteInsert($type, $quote, $lead);

            $randomProjectProviderIdEnabled = $lead->project->params->object->quote->enableRandomProjectProviderId;
            if ($randomProjectProviderIdEnabled && $projectRelationsIds = ProjectRelationQuery::getRelatedProjectIds($lead->project_id)) {
                $projectRelationsCount = count($projectRelationsIds);
                $randomProjectIndex = $projectRelationsCount > 1 ? random_int(0, $projectRelationsCount - 1) : 0;
                $quote->provider_project_id = $projectRelationsIds[$randomProjectIndex] ?? null;
            }

            $currencyCode = $quoteAttributes['currency_code'] ?? null;
            $clientQuotePriceService = new ClientQuotePriceService($quote);
            $clientQuotePriceService->setClientCurrency($currencyCode)->calculateClientCurrencyRate();
            $quote = $clientQuotePriceService->getQuote();

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
                    $quotePrice = QuotePriceCreateService::createFromApi($quote, $quotePriceAttributes, $currencyCode);
                    if (!$quotePrice->save()) {
                        $warnings[] = $quotePrice->getErrorSummary(false)[0];
                    }
                }
            }

            try {
                QuoteLabelService::processingQuoteLabel($quoteAttributes, $quote->id, 'prod_types');
            } catch (\Throwable $throwable) {
                $warnings[] = $throwable->getMessage();
            }

            if (!$quote->hasErrors()) {
                $response['status'] = 'Success';
                if ($lead->called_expert) {
                    LeadPoorProcessingService::addLeadPoorProcessingJob(
                        $lead->id,
                        [LeadPoorProcessingDataDictionary::KEY_EXPERT_IDLE],
                        LeadPoorProcessingLogStatus::REASON_EXPERT_IDLE
                    );
                }
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

            $errorMessage = ($throwable instanceof AdditionalDataException) ?
                ArrayHelper::merge(AppHelper::throwableLog($throwable), $throwable->getAdditionalData()) :
                AppHelper::throwableLog($throwable);

            if ($throwable->getCode() < 0) {
                Yii::warning($errorMessage, 'API:Quote:create:warning:try');
            } else {
                Yii::error($errorMessage, 'API:Quote:create:try');
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

        $responseData = $this->apiLog->endApiLog($responseData);

        return $responseData;
    }

    /**
     *
     * @api {post} /v1/quote/update Update Quote
     * @apiVersion 0.1.0
     * @apiName UpdateQuote
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
     * @apiParam {object}           Quote                       Quote data array
     * @apiParam {string}           [Quote.uid]                 uid
     * @apiParam {bool}             [Quote.needSync]            needSync
     * @apiParam {string}           [Quote.main_airline_code]   main_airline_code
     * @apiParam {string}           [Quote.reservation_dump]    reservation_dump
     * @apiParam {int}              [Quote.status]              status
     * @apiParam {string}           [Quote.check_payment]       check_payment
     * @apiParam {string}           [Quote.fare_type]           fare_type
     * @apiParam {string}           [Quote.employee_name]       employee_name
     * @apiParam {bool}             [Quote.created_by_seller]   created_by_seller
     * @apiParam {int}              [Quote.type_id]             type_id
     * @apiParam {object}           [Quote.prod_types[]]        Quote labels
     * @apiParam {object}           [Quote.baggage[]]           Quote baggage
     * @apiParam {object}           [Quote.baggage.segment[]]      Quote baggage segment
     * @apiParam {object}           [Quote.baggage.free_baggage[]] Quote baggage segment
     * @apiParam {int}              [Quote.baggage.free_baggage.piece] Quote free baggage piece number
     * @apiParam {string}           [Quote.baggage.free_baggage.weight] Quote free baggage weight
     * @apiParam {string}           [Quote.baggage.free_baggage.height] Quote free baggage height
     * @apiParam {object}           [Quote.baggage.paid_baggage[]] Quote paid baggage
     * @apiParam {int}               [Quote.baggage.paid_baggage.piece] Quote paid baggage piece number
     * @apiParam {string}           [Quote.baggage.paid_baggage.weight] Quote paid baggage weight
     * @apiParam {string}           [Quote.baggage.paid_baggage.height] Quote paid baggage height
     * @apiParam {string}           [Quote.baggage.paid_baggage.price] Quote paid baggage price
     * @apiParam {object}           [Lead[]]                    Lead data array
     * @apiParam {string}           [Lead.uid]                  uid
     * @apiParam {int}              [Lead.market_info_id]       market_info_id
     * @apiParam {int}              [Lead.bo_flight_id]         bo_flight_id
     * @apiParam {float}            [Lead.final_profit]         final_profit
     * @apiParam {array}            [Lead.additional_information[]]  additional information array
     * @apiParam {object}           [QuotePrice[]]                QuotePrice data array
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
     *          "type_id" : 0,
     *          "baggage" : [],
     *          "prod_types" : ["SEP", "TOUR"]
     *      },
     *      "Lead": {
     *          "uid": "5de486f15f095",
     *          "market_info_id": 52,
     *          "bo_flight_id": 0,
     *          "final_profit": 0
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
     * @apiSuccess {array} errors     Errors
     * @apiSuccess {string} action    Action
     * @apiSuccess {integer} response_id    Response Id
     * @apiSuccess {DateTime} request_dt    Request Date & Time
     * @apiSuccess {DateTime} response_dt   Response Date & Time
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     *  {
     *      "status": "Success",
     *      "errors":[],
     *      "action": "v1/quote/update",
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
     *       "message": "Not found Quote UID: 5f207ec201b19",
     *       "code": 2,
     *       "status": 404,
     *       "type": "yii\\web\\NotFoundHttpException"
     *   }
     *
     *
     * @apiErrorExample {json} Error-Response (400):
     *
     * HTTP/1.1 400 Bad Request
     * {
     *  "status":400,
     *  "message":"Quote.uid is required",
     *  "code":"1",
     *  "errors":[]
     * }
     *
     *
     * @return mixed
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     * @throws \yii\db\Exception
     */
    public function actionUpdate()
    {
        $this->checkPost();
        $this->startApiLog($this->action->uniqueId);

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
                        if (!$model->lead->hybrid_uid) {
                            $model->lead->hybrid_uid = $leadAttributes['additional_information'][0]['bookingId'] ?? null;
                        }
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
        $responseData = $this->apiLog->endApiLog($responseData);

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
     * @api {post} /v1/quote/create-data Create Flight Quote by origin search data
     * @apiVersion 1.0.0
     * @apiName CreateQuoteData
     * @apiGroup Quotes
     * @apiPermission Authorized User
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {Integer}          lead_id                     Lead Id
     * @apiParam {String}           origin_search_data          Origin Search Data from air search service <code>Valid JSON</code>
     * @apiParam {String{..max 50}} [provider_project_key]      Project Key
     *
     * @apiParamExample {json} Request-Example:
     * {
            "lead_id": 513145,
            "origin_search_data": "{\"key\":\"2_U0FMMTAxKlkyMTAwL0tJVkxPTjIwMjEtMTEtMTcqUk9+I1JPMjAyI1JPMzkxfmxjOmVuX3Vz\",\"routingId\":1,\"prices\":{\"lastTicketDate\":\"2021-05-05\",\"totalPrice\":408.9,\"totalTax\":99.9,\"comm\":0,\"isCk\":false,\"markupId\":0,\"markupUid\":\"\",\"markup\":0},\"passengers\":{\"ADT\":{\"codeAs\":\"JWZ\",\"cnt\":2,\"baseFare\":103,\"pubBaseFare\":103,\"baseTax\":33.3,\"markup\":0,\"comm\":0,\"price\":136.3,\"tax\":33.3,\"oBaseFare\":{\"amount\":103,\"currency\":\"USD\"},\"oBaseTax\":{\"amount\":33.3,\"currency\":\"USD\"}},\"CHD\":{\"codeAs\":\"JWC\",\"cnt\":1,\"baseFare\":103,\"pubBaseFare\":103,\"baseTax\":33.3,\"markup\":0,\"comm\":0,\"price\":136.3,\"tax\":33.3,\"oBaseFare\":{\"amount\":103,\"currency\":\"USD\"},\"oBaseTax\":{\"amount\":33.3,\"currency\":\"USD\"}}},\"penalties\":{\"exchange\":true,\"refund\":false,\"list\":[{\"type\":\"ex\",\"applicability\":\"before\",\"permitted\":true,\"amount\":72,\"oAmount\":{\"amount\":72,\"currency\":\"USD\"}},{\"type\":\"ex\",\"applicability\":\"after\",\"permitted\":true,\"amount\":72,\"oAmount\":{\"amount\":72,\"currency\":\"USD\"}},{\"type\":\"re\",\"applicability\":\"before\",\"permitted\":false},{\"type\":\"re\",\"applicability\":\"after\",\"permitted\":false}]},\"trips\":[{\"tripId\":1,\"segments\":[{\"segmentId\":1,\"departureTime\":\"2021-11-17 09:30\",\"arrivalTime\":\"2021-11-17 10:45\",\"stop\":0,\"stops\":[],\"flightNumber\":\"202\",\"bookingClass\":\"E\",\"duration\":75,\"departureAirportCode\":\"KIV\",\"departureAirportTerminal\":\"\",\"arrivalAirportCode\":\"OTP\",\"arrivalAirportTerminal\":\"\",\"operatingAirline\":\"RO\",\"airEquipType\":\"AT7\",\"marketingAirline\":\"RO\",\"marriageGroup\":\"I\",\"mileage\":215,\"cabin\":\"Y\",\"meal\":\"\",\"fareCode\":\"EOWSVRMD\",\"baggage\":{\"ADT\":{\"carryOn\":true,\"allowPieces\":1},\"CHD\":{\"carryOn\":true,\"allowPieces\":1}},\"recheckBaggage\":false},{\"segmentId\":2,\"departureTime\":\"2021-11-17 12:20\",\"arrivalTime\":\"2021-11-17 14:05\",\"stop\":0,\"stops\":[],\"flightNumber\":\"391\",\"bookingClass\":\"E\",\"duration\":225,\"departureAirportCode\":\"OTP\",\"departureAirportTerminal\":\"\",\"arrivalAirportCode\":\"LHR\",\"arrivalAirportTerminal\":\"4\",\"operatingAirline\":\"RO\",\"airEquipType\":\"73H\",\"marketingAirline\":\"RO\",\"marriageGroup\":\"O\",\"mileage\":1292,\"cabin\":\"Y\",\"meal\":\"\",\"fareCode\":\"EOWSVRGB\",\"baggage\":{\"ADT\":{\"carryOn\":true,\"allowPieces\":1},\"CHD\":{\"carryOn\":true,\"allowPieces\":1}},\"recheckBaggage\":false}],\"duration\":395}],\"maxSeats\":3,\"paxCnt\":3,\"validatingCarrier\":\"RO\",\"gds\":\"T\",\"pcc\":\"DVI\",\"cons\":\"GTT\",\"fareType\":\"PUB\",\"tripType\":\"OW\",\"cabin\":\"Y\",\"currency\":\"USD\",\"currencies\":[\"USD\"],\"currencyRates\":{\"USDUSD\":{\"from\":\"USD\",\"to\":\"USD\",\"rate\":1}},\"keys\":{\"travelport\":{\"traceId\":\"908f70b5-cbe1-4800-89e2-1f0496cc1502\",\"availabilitySources\":\"A,A\",\"type\":\"T\"},\"seatHoldSeg\":{\"trip\":0,\"segment\":0,\"seats\":3}},\"meta\":{\"eip\":0,\"noavail\":false,\"searchId\":\"U0FMMTAxWTIxMDB8S0lWTE9OMjAyMS0xMS0xNw==\",\"lang\":\"en\",\"group1\":\"KIVLON:RORO:0:408.90\",\"rank\":10,\"cheapest\":true,\"fastest\":false,\"best\":true,\"bags\":1,\"country\":\"us\",\"prod_types\":[\"PUB\"]}}",
            "provider_project_key": "hop2"
        }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     * {
            "status": 200,
            "message": "OK",
            "data": {
                "quote_uid": "609259bfe52b9"
            }
        }
     *
     * @apiErrorExample {json} Error-Response (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     * {
            "status": 422,
            "message": "Validation error",
            "errors": {
                "lead_id": [
                    "Lead Id is invalid."
                ]
            },
            "code": 0
        }
     *
     * @apiErrorExample {json} Error-Response (422):
     *
     * HTTP/1.1 422 Validation Error
     * {
            "status": 422,
            "message": "Error",
            "errors": [
                "Not found project relation by key: ovago"
            ],
            "code": 0
        }
     *
     * @apiErrorExample {json} Error-Response (400):
     *
     * HTTP/1.1 400 Bad Request
     *
     * {
            "status": 400,
            "message": "Load data error",
            "errors": [
                "Not found data on POST request"
            ],
            "code": 0
        }
     *
     *
     */
    public function actionCreateData()
    {
        $form = new QuoteCreateDataForm();
        $warnings = [];

        if (!Yii::$app->request->isPost) {
            return new ErrorResponse(
                new StatusCodeMessage(405),
                new MessageMessage('Method not allowed'),
                new ErrorsMessage('Method not allowed'),
            );
        }

        if (!$form->load(Yii::$app->request->post())) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found data on POST request'),
            );
        }

        $this->startApiLog($this->action->uniqueId);

        if (!$form->validate()) {
            $response = new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($form->getErrors())
            );
            $this->apiLog->endApiLog(ArrayHelper::toArray($response));
            return $response;
        }

        try {
            $lead = $this->leadRepository->find($form->lead_id);

            $this->apiProject = $this->apiProject ?: Project::findOne($lead->project_id);

            if (!$this->apiProject) {
                throw new \RuntimeException(
                    'ApiProject not detected. Create quota is not possible.',
                    ApiCodeException::NOT_FOUND_PROJECT_CURRENT_USER
                );
            }

            $projectProviderId = null;
            if ($form->provider_project_key) {
                $projectRelation = $this->projectRelationRepository->findByRelatedProjectKey($this->apiProject->id, $form->provider_project_key);
                $projectProviderId = $projectRelation->prl_related_project_id;
            }

            $randomProjectProviderIdEnabled = $lead->project->params->object->quote->enableRandomProjectProviderId;
            if (!$projectProviderId && $randomProjectProviderIdEnabled && $projectRelationsIds = ProjectRelationQuery::getRelatedProjectIds($lead->project_id)) {
                $projectRelationsCount = count($projectRelationsIds);
                $randomProjectIndex = $projectRelationsCount > 1 ? random_int(0, $projectRelationsCount - 1) : 0;
                $projectProviderId = $projectRelationsIds[$randomProjectIndex] ?? null;
            }

            $preparedQuoteData = QuoteHelper::formatQuoteData(['results' => [JsonHelper::decode($form->origin_search_data)]]);
            $quoteUid = $this->addQuoteService->createByData($preparedQuoteData['results'][0], $lead, $projectProviderId);
        } catch (\DomainException | \RuntimeException $e) {
            $response = new ErrorResponse(
                new MessageMessage('Error'),
                new ErrorsMessage($e->getMessage()),
                new CodeMessage($e->getCode())
            );
            $this->apiLog->endApiLog(ArrayHelper::toArray($response));
            return $response;
        } catch (\Throwable $e) {
            \Yii::error(AppHelper::throwableLog($e, true), 'API:QuoteController:actionCreateData:Throwable');
            $response = new ErrorResponse(
                new ErrorsMessage('An error occurred while creating a quote'),
                new CodeMessage($e->getCode())
            );
            $this->apiLog->endApiLog(ArrayHelper::toArray($response));
            return $response;
        }

        try {
            if ($quote = Quote::findOne(['uid' => $quoteUid])) {
                QuoteLabelService::processingQuoteLabel($preparedQuoteData['results'][0], $quote->id);
            }
        } catch (\Throwable $throwable) {
            \Yii::warning($throwable->getMessage(), 'QuoteController:actionCreateData:QuoteLabel');
            $warnings[] = $throwable->getMessage();
        }

        $responseObj = new SuccessResponse(
            new DataMessage(
                new Message('quote_uid', $quoteUid)
            )
        );

        $response = ArrayHelper::toArray($responseObj);
        if ($warnings) {
            ArrayHelper::setValue($response, 'warnings', implode(',', $warnings));
        }

        $this->apiLog->endApiLog($response);
        return $response;
    }

    /**
     * @api {post} /v1/quote/create-key Create Flight Quote by key
     * @apiVersion 1.0.0
     * @apiName CreateQuoteKey
     * @apiGroup Quotes
     * @apiPermission Authorized User
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {Integer}          lead_id                     Lead Id
     * @apiParam {String}           offer_search_key            Search key
     * @apiParam {String{..max 50}} [provider_project_key]      Project Key
     *
     * @apiParamExample {json} Request-Example:
     * {
            "lead_id": 513146,
            "offer_search_key": "2_U0FMMTAxKlkyMTAwL0tJVkxPTjIwMjEtMTEtMTcqUk9+I1JPMjAyI1JPMzkxfmxjOmVuX3Vz",
            "provider_project_key": "hop2"
        }
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     * {
            "status": 200,
            "message": "OK",
            "data": {
                "quote_uid": "609259bfe52b9"
            }
        }
     *
     * @apiErrorExample {json} Error-Response (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     * {
            "status": 422,
            "message": "Validation error",
            "errors": {
                "lead_id": [
                    "Lead Id is invalid."
                ]
            },
            "code": 0
        }
     *
     * @apiErrorExample {json} Error-Response (422):
     *
     * HTTP/1.1 422 Validation Error
     * {
            "status": 422,
            "message": "Error",
            "errors": [
                "Not found project relation by key: ovago"
            ],
            "code": 0
        }
     *
     * @apiErrorExample {json} Error-Response (400):
     *
     * HTTP/1.1 400 Bad Request
     *
     * {
            "status": 400,
            "message": "Load data error",
            "errors": [
                "Not found data on POST request"
            ],
            "code": 0
        }
     *
     *
     */
    public function actionCreateKey()
    {
        $form = new QuoteCreateKeyForm();
        $warnings = [];

        if (!Yii::$app->request->isPost) {
            return new ErrorResponse(
                new StatusCodeMessage(405),
                new MessageMessage('Method not allowed'),
                new ErrorsMessage('Method not allowed'),
            );
        }

        if (!$form->load(Yii::$app->request->post())) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found data on POST request'),
            );
        }

        $this->startApiLog($this->action->uniqueId);

        if (!$form->validate()) {
            $response = new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($form->getErrors())
            );
            $this->apiLog->endApiLog(ArrayHelper::toArray($response));
            return $response;
        }

        try {
            $lead = $this->leadRepository->find($form->lead_id);

            $this->apiProject = $this->apiProject ?: Project::findOne($lead->project_id);

            if (!$this->apiProject) {
                throw new \RuntimeException(
                    'ApiProject not detected. Create quota is not possible.',
                    ApiCodeException::NOT_FOUND_PROJECT_CURRENT_USER
                );
            }

            $projectProviderId = null;
            if ($form->provider_project_key) {
                $projectRelation = $this->projectRelationRepository->findByRelatedProjectKey($this->apiProject->id, $form->provider_project_key);
                $projectProviderId = $projectRelation->prl_related_project_id;
            }

            $randomProjectProviderIdEnabled = $lead->project->params->object->quote->enableRandomProjectProviderId;
            if (!$projectProviderId && $randomProjectProviderIdEnabled && $projectRelationsIds = ProjectRelationQuery::getRelatedProjectIds($lead->project_id)) {
                $projectRelationsCount = count($projectRelationsIds);
                $randomProjectIndex = $projectRelationsCount > 1 ? random_int(0, $projectRelationsCount - 1) : 0;
                $projectProviderId = $projectRelationsIds[$randomProjectIndex] ?? null;
            }

            $searchQuoteRequest = SearchService::getOnlineQuoteByKey($form->offer_search_key);
            if (empty($searchQuoteRequest['data'])) {
                throw new \RuntimeException('Quote not found by key: ' . $form->offer_search_key);
            }
            $preparedQuoteData = QuoteHelper::formatQuoteData(['results' => [$searchQuoteRequest['data']]]);
            $quoteUid = $this->addQuoteService->createByData($preparedQuoteData['results'][0], $lead, $projectProviderId);
        } catch (\DomainException | \RuntimeException $e) {
            $response = new ErrorResponse(
                new MessageMessage('Error'),
                new ErrorsMessage($e->getMessage()),
                new CodeMessage($e->getCode())
            );
            $this->apiLog->endApiLog(ArrayHelper::toArray($response));
            return $response;
        } catch (\Throwable $e) {
            \Yii::error(AppHelper::throwableLog($e, true), 'API:QuoteController:actionCreateKey:Throwable');
            $response = new ErrorResponse(
                new ErrorsMessage('An error occurred while creating a quote'),
                new CodeMessage($e->getCode())
            );
            $this->apiLog->endApiLog(ArrayHelper::toArray($response));
            return $response;
        }

        try {
            if ($quote = Quote::findOne(['uid' => $quoteUid])) {
                QuoteLabelService::processingQuoteLabel($preparedQuoteData['results'][0], $quote->id);
            }
        } catch (\Throwable $throwable) {
            \Yii::warning($throwable->getMessage(), 'QuoteController:actionCreateKey:QuoteLabel');
            $warnings[] = $throwable->getMessage();
        }

        $responseObj = new SuccessResponse(
            new DataMessage(
                new Message('quote_uid', $quoteUid)
            )
        );

        $response = ArrayHelper::toArray($responseObj);
        if ($warnings) {
            ArrayHelper::setValue($response, 'warnings', implode(',', $warnings));
        }

        $this->apiLog->endApiLog($response);
        return $response;
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

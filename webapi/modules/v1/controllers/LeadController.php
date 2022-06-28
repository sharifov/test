<?php

namespace webapi\modules\v1\controllers;

use common\components\jobs\AutoAddQuoteJob;
use common\components\jobs\WebEngageLeadRequestJob;
use common\components\purifier\Purifier;
use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Lead;
use common\models\LeadCallExpert;
use common\models\LeadFlightSegment;
use common\models\Notifications;
use common\models\Sources;
use common\models\VisitorLog;
use frontend\helpers\RedisHelper;
use frontend\widgets\notification\NotificationMessage;
use modules\experiment\models\ExperimentTarget;
use modules\featureFlag\FFlag;
use modules\flight\models\FlightQuoteSegment;
use modules\flight\models\FlightSegment;
use modules\product\src\useCases\product\api\create\flight\Handler;
use modules\webEngage\settings\WebEngageDictionary;
use modules\webEngage\src\service\webEngageEventData\lead\eventData\LeadCreatedEventData;
use src\helpers\app\AppHelper;
use src\helpers\text\HashHelper;
use src\model\clientData\service\ClientDataService;
use src\model\leadData\entity\LeadData;
use src\model\leadData\services\LeadDataCreateService;
use src\model\leadData\services\LeadDataService;
use src\repositories\lead\LeadRepository;
use src\services\lead\calculator\LeadTripTypeCalculator;
use src\services\lead\calculator\SegmentDTO;
use src\services\lead\LeadCreateApiService;
use src\services\lead\LeadHashGenerator;
use src\services\quote\addQuote\AddQuoteService;
use src\services\TransactionManager;
use webapi\models\ApiLead;
use webapi\models\ApiLeadCallExpert;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\VarDumper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;

class LeadController extends ApiBaseController
{
    private $leadHashGenerator;
    private $leadRepository;
    private $transactionManager;
    private $leadCreateApiService;
    private $createProductFlightHandler;
    private AddQuoteService $autoQuoteService;

    public function __construct(
        $id,
        $module,
        LeadHashGenerator $leadHashGenerator,
        LeadRepository $leadRepository,
        TransactionManager $transactionManager,
        LeadCreateApiService $leadCreateApiService,
        AddQuoteService $autoQuoteService,
        Handler $createProductFlightHandler,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->leadHashGenerator = $leadHashGenerator;
        $this->leadRepository = $leadRepository;
        $this->transactionManager = $transactionManager;
        $this->leadCreateApiService = $leadCreateApiService;
        $this->createProductFlightHandler = $createProductFlightHandler;
        $this->autoQuoteService = $autoQuoteService;
    }

    /**
     *
     * @api {post} /v1/lead/create Create Lead
     * @apiVersion 0.1.0
     * @apiName CreateLead
     * @apiGroup Leads
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization    Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string}           [apiKey]   API Key for Project (if not use Basic-Authorization)
     * @apiParam {object}           lead                                               Lead data array
     * @apiParam {int}                  [lead.source_id]                                   Source ID
     * @apiParam {string{20}}           lead.sub_sources_code                              Source Code
     * @apiParam {int{1..9}}            lead.adults                                        Adult count
     * @apiParam {string{1}=E-ECONOMY,B-BUSINESS,F-FIRST,P-PREMIUM}        lead.cabin                                         Cabin
     * @apiParam {array[]}              lead.emails                                         Array of Emails (string)
     * @apiParam {array[]}              lead.phones                                         Array of Phones (string)
     * @apiParam {object[]}             lead.flights                                        Array of Flights
     * @apiParam {string{3}}                                lead.flights.origin                 Flight Origin location Airport IATA-code
     * @apiParam {string{3}}                                lead.flights.destination            Flight Destination location Airport IATA-code
     * @apiParam {datetime{YYYY-MM-DD HH:II:SS}}            lead.flights.departure              Flight Departure DateTime (format YYYY-MM-DD HH:ii:ss)
     * @apiParam {string{2}=OW-ONE_WAY,RT-ROUND_TRIP,MC-MULTI_DESTINATION}        [lead.trip_type]                                         Trip type (if empty - autocomplete)
     * @apiParam {int=1-PENDING,2-PROCESSING,4-REJECT,5-FOLLOW_UP,8-ON_HOLD,10-SOLD,11-TRASH,12-BOOKED,13-SNOOZE}        [lead.status]                                       Status
     *
     * @apiParam {int{0..9}}            [lead.children]                                      Children count
     * @apiParam {int{0..9}}            [lead.infants]                                       Infant count
     * @apiParam {string{40}}           [lead.uid]                                           UID value
     * @apiParam {text}                 [lead.notes_for_experts]                             Notes for expert
     * @apiParam {text}                 [lead.request_ip_detail]                             Request IP detail (autocomplete)
     * @apiParam {string{50}}           [lead.request_ip]                                    Request IP
     * @apiParam {int}                  [lead.snooze_for]                                    Snooze for
     * @apiParam {int}                  [lead.rating]                                        Rating
     * @apiParam {int}                  [lead.discount_id]                                   Discount Id
     * @apiParam {string{..3}}          [lead.currency_code]                                 Client currency code
     *
     * @apiParam {string{3..100}}       [lead.client_first_name]                            Client first name
     * @apiParam {string{3..100}}       [lead.client_last_name]                             Client last name
     * @apiParam {string{3..100}}       [lead.client_middle_name]                           Client middle name
     * @apiParam {string{20}}           lead.user_language                                  User language
     * @apiParam {bool}                 [lead.is_test]                                      Is test lead (default false)
     * @apiParam {datetime{YYYY-MM-DD HH:mm:ss}}  [lead.expire_at]                          Expire at
     *
     * @apiParam {object[]}             lead.visitor_log                                    Array of Visitor log
     * @apiParam {string{10}}           lead.visitor_log.vl_source_cid
     * @apiParam {string{36}}           lead.visitor_log.vl_ga_client_id
     * @apiParam {string{36}}           lead.visitor_log.vl_ga_user_id
     * @apiParam {int}                  lead.visitor_log.vl_customer_id
     * @apiParam {string{100}}          lead.visitor_log.vl_gclid
     * @apiParam {string{255}}          lead.visitor_log.vl_dclid
     * @apiParam {string{50}}           lead.visitor_log.vl_utm_source
     * @apiParam {string{50}}           lead.visitor_log.vl_utm_medium
     * @apiParam {string{50}}           lead.visitor_log.vl_utm_campaign
     * @apiParam {string{50}}           lead.visitor_log.vl_utm_term
     * @apiParam {string{50}}           lead.visitor_log.vl_utm_content
     * @apiParam {string{500}}          lead.visitor_log.vl_referral_url
     * @apiParam {string{500}}          lead.visitor_log.vl_location_url
     * @apiParam {string{500}}          lead.visitor_log.vl_user_agent
     * @apiParam {string{39}}           lead.visitor_log.vl_ip_address
     * @apiParam {object[]}             [lead.lead_data]                         Array of Lead Data
     * @apiParam {string{50}}           [lead.lead_data.field_key]               Lead Data Key
     * @apiParam {string{500}}          [lead.lead_data.field_value]             Lead Data Value
     * @apiParam {object[]}             [lead.client_data]                       Array of Client Data
     * @apiParam {object[]}             [lead.experiments]                       Array of Experiment codes
     * @apiParam {string{50}}           [lead.client_data.field_key]             Client Data Key
     * @apiParam {string{500}}          [lead.client_data.field_value]           Client Data Value
     * @apiParam {datetime{YYYY-MM-DD HH:mm:ss}}  lead.visitor_log.vl_visit_dt
     * @apiParam {object}               Client
     * @apiParam {string}               [Client.name]                            Client name
     * @apiParam {string}               [Client.phone]                           Client phone
     * @apiParam {string}               [Client.email]                           Client email
     * @apiParam {string}               [Client.client_ip]                       Client client_ip
     * @apiParam {string}               [Client.uuid]                            Client uuid
     *
     * @apiParamExample {json} Request-Example:
     * {
     *    "apiKey": "d190c378e131ccfd8a889c8ee8994cb55f22fbeeb93f9b99007e8e7ecc24d0dd",
     *    "lead": {
     *        "flights": [
     *            {
     *                "origin": "KIV",
     *                "destination": "DME",
     *                "departure": "2018-10-13 13:50:00",
     *            },
     *            {
     *                "origin": "DME",
     *                "destination": "KIV",
     *                "departure": "2018-10-18 10:54:00",
     *            }
     *        ],
     *        "emails": [
     *          "email1@gmail.com",
     *          "email2@gmail.com",
     *        ],
     *        "phones": [
     *          "+373-69-487523",
     *          "022-45-7895-89",
     *        ],
     *        "source_id": 38,
     *        "sub_sources_code": "BBM101",
     *        "adults": 1,
     *        "client_first_name": "Alexandr",
     *        "client_last_name": "Freeman",
     *        "user_language": "en-GB",
     *        "is_test": true,
     *        "expire_at": "2020-01-20 12:12:12",
     *        "currency_code": "USD",
     *        "lead_data": [
     *               {
     *                  "field_key": "example_key",
     *                  "field_value": "example_value"
     *              },
     *              {
     *                  "field_key": "cross_system_xp",
     *                  "field_value": "wpl5.0"
     *              },
     *              {
     *                  "field_key": "cross_system_xp",
     *                  "field_value": "wpl6.1"
     *              }
     *        ],
     *       "experiments": [
     *           { "cross_ex_code": "123.1" },
     *           { "cross_ex_code": "123.4" }
     *       ]
     *       "client_data": [
     *               {
     *                  "field_key": "example_key",
     *                  "field_value": "example_value"
     *              }
     *        ],
     *        "visitor_log": [
     *               {
     *                   "vl_source_cid": "string_abc",
     *                   "vl_ga_client_id": "35009a79-1a05-49d7-b876-2b884d0f825b",
     *                   "vl_ga_user_id": "35009a79-1a05-49d7-b876-2b884d0f825b",
     *                   "vl_customer_id": "3",
     *                   "vl_gclid": "gclid=TeSter-123#bookmark",
     *                   "vl_dclid": "CJKu8LrQxd4CFQ1qwQodmJIElw",
     *                   "vl_utm_source": "newsletter4",
     *                   "vl_utm_medium": "string_abc",
     *                   "vl_utm_campaign": "string_abc",
     *                   "vl_utm_term": "string_abc",
     *                   "vl_utm_content": "string_abc",
     *                   "vl_referral_url": "string_abc",
     *                   "vl_location_url": "string_abc",
     *                   "vl_user_agent": "string_abc",
     *                   "vl_ip_address": "127.0.0.1",
     *                   "vl_visit_dt": "2020-02-14 12:00:00"
     *               },
     *               {
     *                   "vl_source_cid": "string_abc",
     *                   "vl_ga_client_id": "35009a79-1a05-49d7-b876-2b884d0f825b",
     *                   "vl_ga_user_id": "35009a79-1a05-49d7-b876-2b884d0f825b",
     *                   "vl_customer_id": "3",
     *                   "vl_gclid": "gclid=TeSter-123#bookmark",
     *                   "vl_dclid": "CJKu8LrQxd4CFQ1qwQodmJIElw",
     *                   "vl_utm_source": "newsletter4",
     *                   "vl_utm_medium": "string_abc",
     *                   "vl_utm_campaign": "string_abc",
     *                   "vl_utm_term": "string_abc",
     *                   "vl_utm_content": "string_abc",
     *                   "vl_referral_url": "string_abc",
     *                   "vl_location_url": "string_abc",
     *                   "vl_user_agent": "string_abc",
     *                   "vl_ip_address": "127.0.0.1",
     *                   "vl_visit_dt": "2020-02-14 12:00:00"
     *               }
     *        ]
     *    },
     *    "Client": {
     *        "name": "Alexandr",
     *        "phone": "+373-69-487523",
     *        "email": "email1@gmail.com",
     *        "client_ip": "127.0.0.1",
     *        "uuid": "35009a79-1a05-49d7-b876-2b884d0f825b"
     *    }
     * }
     *
     * @apiSuccess {Integer} response_id    Response Id
     * @apiSuccess {DateTime} request_dt    Request Date & Time
     * @apiSuccess {DateTime} response_dt   Response Date & Time
     * @apiSuccess {Array} data Data Array
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     * {
     *   "status": 200,
     *   "name": "Success",
     *   "code": 0,
     *   "message": "",
     *   "data": {
     *       "response": {
     *           "lead": {
     *               "client_id": 11,
     *               "employee_id": null,
     *               "status": 1,
     *               "uid": "5b73b80eaf69b",
     *               "gid": "65df1546edccce15518e929e5af1a4",
     *               "project_id": 6,
     *               "source_id": "38",
     *               "trip_type": "RT",
     *               "cabin": "E",
     *               "adults": "1",
     *               "children": 0,
     *               "infants": 0,
     *               "notes_for_experts": null,
     *               "created": "2018-08-15 05:20:14",
     *               "updated": "2018-08-15 05:20:14",
     *               "request_ip": "127.0.0.1",
     *               "request_ip_detail": "{\"ip\":\"127.0.0.1\",\"city\":\"North Pole\",\"postal\":\"99705\",\"state\":\"Alaska\",\"state_code\":\"AK\",\"country\":\"United States\",\"country_code\":\"US\",\"location\":\"64.7548317,-147.3431046\",\"timezone\":{\"id\":\"America\\/Anchorage\",\"location\":\"61.21805,-149.90028\",\"country_code\":\"US\",\"country_name\":\"United States of America\",\"iso3166_1_alpha_2\":\"US\",\"iso3166_1_alpha_3\":\"USA\",\"un_m49_code\":\"840\",\"itu\":\"USA\",\"marc\":\"xxu\",\"wmo\":\"US\",\"ds\":\"USA\",\"phone_prefix\":\"1\",\"fifa\":\"USA\",\"fips\":\"US\",\"gual\":\"259\",\"ioc\":\"USA\",\"currency_alpha_code\":\"USD\",\"currency_country_name\":\"UNITED STATES\",\"currency_minor_unit\":\"2\",\"currency_name\":\"US Dollar\",\"currency_code\":\"840\",\"independent\":\"Yes\",\"capital\":\"Washington\",\"continent\":\"NA\",\"tld\":\".us\",\"languages\":\"en-US,es-US,haw,fr\",\"geoname_id\":\"6252001\",\"edgar\":\"\"},\"datetime\":{\"date\":\"08\\/14\\/2018\",\"date_time\":\"08\\/14\\/2018 21:20:15\",\"date_time_txt\":\"Tuesday, August 14, 2018 21:20:15\",\"date_time_wti\":\"Tue, 14 Aug 2018 21:20:15 -0800\",\"date_time_ymd\":\"2018-08-14T21:20:15-08:00\",\"time\":\"21:20:15\",\"month\":\"8\",\"month_wilz\":\"08\",\"month_abbr\":\"Aug\",\"month_full\":\"August\",\"month_days\":\"31\",\"day\":\"14\",\"day_wilz\":\"14\",\"day_abbr\":\"Tue\",\"day_full\":\"Tuesday\",\"year\":\"2018\",\"year_abbr\":\"18\",\"hour_12_wolz\":\"9\",\"hour_12_wilz\":\"09\",\"hour_24_wolz\":\"21\",\"hour_24_wilz\":\"21\",\"hour_am_pm\":\"pm\",\"minutes\":\"20\",\"seconds\":\"15\",\"week\":\"33\",\"offset_seconds\":\"-28800\",\"offset_minutes\":\"-480\",\"offset_hours\":\"-8\",\"offset_gmt\":\"-08:00\",\"offset_tzid\":\"America\\/Anchorage\",\"offset_tzab\":\"AKDT\",\"offset_tzfull\":\"Alaska Daylight Time\",\"tz_string\":\"AKST+9AKDT,M3.2.0\\/2,M11.1.0\\/2\",\"dst\":\"true\",\"dst_observes\":\"true\",\"timeday_spe\":\"evening\",\"timeday_gen\":\"evening\"}}",
     *               "offset_gmt": "-08.00",
     *               "snooze_for": null,
     *               "rating": null,
     *               "id": 7
     *           },
     *           "flights": [
     *               {
     *                   "origin": "BOS",
     *                   "destination": "LGW",
     *                   "departure": "2018-09-19"
     *               },
     *               {
     *                   "origin": "LGW",
     *                   "destination": "BOS",
     *                   "departure": "2018-09-22"
     *               }
     *           ],
     *           "emails": [
     *               "chalpet@gmail.com",
     *               "chalpet2@gmail.com"
     *           ],
     *           "phones": [
     *               "+373-69-98-698",
     *               "+373-69-98-698"
     *           ],
     *          "client": {
     *              "uuid": "35009a79-1a05-49d7-b876-2b884d0f825b"
     *              "client_id": 331968,
     *              "first_name": "Johann",
     *              "middle_name": "Sebastian",
     *              "last_name": "Bach",
     *              "phones": [
     *                 "+13152572166"
     *              ],
     *              "emails": [
     *                 "example@test.com",
     *                 "bah@gmail.com"
     *              ]
     *           },
     *          "leadDataInserted": [
     *              {
     *                  "ld_field_key": "kayakclickid",
     *                  "ld_field_value": "example_value",
     *                  "ld_id": 3
     *              }
     *          ],
     *          "clientDataInserted": [
     *              {
     *                  "cd_field_key": "example_key",
     *                  "cd_field_value": "example_value",
     *              }
     *          ],
     *          "warnings": []
     *       },
     *       "request": {
     *           "client_id": null,
     *           "employee_id": null,
     *           "status": null,
     *           "uid": null,
     *           "project_id": 6,
     *           "source_id": "38",
     *           "trip_type": null,
     *           "cabin": null,
     *           "adults": "1",
     *           "children": null,
     *           "infants": null,
     *           "notes_for_experts": null,
     *           "created": null,
     *           "updated": null,
     *           "request_ip": null,
     *           "request_ip_detail": null,
     *           "offset_gmt": null,
     *           "snooze_for": null,
     *           "rating": null,
     *           "flights": [
     *               {
     *                   "origin": "BOS",
     *                   "destination": "LGW",
     *                   "departure": "2018-09-19"
     *               },
     *               {
     *                   "origin": "LGW",
     *                   "destination": "BOS",
     *                   "departure": "2018-09-22"
     *               }
     *           ],
     *           "emails": [
     *               "chalpet@gmail.com",
     *               "chalpet2@gmail.com"
     *           ],
     *           "phones": [
     *               "+373-69-98-698",
     *               "+373-69-98-698"
     *           ],
     *           "client_first_name": "Alexandr",
     *           "client_last_name": "Freeman"
     *       }
     *   },
     *   "action": "v1/lead/create",
     *   "response_id": 42,
     *   "request_dt": "2018-08-15 05:20:14",
     *   "response_dt": "2018-08-15 05:20:15"
     * }
     *
     * @apiError UserNotFound The id of the User was not found.
     *
     * @apiErrorExample Error-Response:
     *      HTTP/1.1 422 Unprocessable entity
     *      {
     *          "name": "Unprocessable entity",
     *          "message": "Flight [0]: Destination should contain at most 3 characters.",
     *          "code": 5,
     *          "status": 422,
     *          "type": "yii\\web\\UnprocessableEntityHttpException"
     *      }
     *
     * @return array
     * @throws BadRequestHttpException
     * @throws UnprocessableEntityHttpException
     * @throws \Throwable
     */
    public function actionCreate()
    {
        $this->checkPost();
        $this->startApiLog($this->action->uniqueId);
        $warnings = [];

        $lead = Yii::$app->request->post('Lead');

        if ($lead) {
            if (isset($lead['client_id'])) {
                unset($lead['client_id']);
            }

            if (isset($lead['source_id'])) {
                unset($lead['source_id']);
            }

            $flights = Yii::$app->request->post('LeadFlights');
            $client = Yii::$app->request->post('Client');

            if ($client) {
                if (isset($client['name']) && $client['name']) {
                    $clientNameArr = explode(' ', $client['name']);
                    if (isset($clientNameArr[1])) {
                        $lead['client_first_name'] = $clientNameArr[0];
                        $lead['client_last_name'] = $clientNameArr[1];
                    } else {
                        $lead['client_first_name'] = $client['name'];
                    }
                }

                if (isset($client['phone']) && $client['phone']) {
                    $lead['phones'][] = $client['phone'];
                }

                if (isset($client['email']) && $client['email']) {
                    $lead['emails'][] = $client['email'];
                }

                if (isset($client['client_ip']) && $client['client_ip']) {
                    $lead['request_ip'] = $client['client_ip'];
                }
            }

            $post['lead'] = $lead;
            $post['lead']['flights'] = $flights;
        } else {
            $post = Yii::$app->request->post();
        }

        if (!empty($post['Client']['uuid'])) {
            $post['lead']['clientUuid'] = $post['Client']['uuid'];
        }

        $modelLead = new ApiLead();

        //$modelLead->scenario = ApiLead::SCENARIO_CREATE;

        //print_r($this->apiProject); exit;

        //print_r($post); exit;

        if ($this->apiProject) {
            $modelLead->project_id = $this->apiProject->id;
        } else {
            $modelLead->project_id = Yii::$app->user->identity->au_project_id;
        }

        if ($modelLead->load($post)) {
            if ($modelLead->project_id) {
                $source = Sources::findOne(['cid' => $modelLead->sub_sources_code, 'project_id' => $modelLead->project_id]);

                if (!$source) {
                    $old_sub_sources_code = $modelLead->sub_sources_code;
                    $source = Sources::find()->where(['project_id' => $modelLead->project_id])->orderBy(['id' => SORT_ASC])->one();
                    if ($source) {
                        $modelLead->source_id = $source->id;
                        $modelLead->sub_sources_code = $source->cid;
                    }
                    Yii::warning('Not found Source Code (' . $old_sub_sources_code . ') Set Default Source (' . $modelLead->sub_sources_code . ') Project Id: ' . $modelLead->project_id, 'API:Lead:create:ApiLead:validate');
                }
            }

            if (!$modelLead->validate()) {
                if ($errors = $modelLead->getErrors()) {
                    throw new UnprocessableEntityHttpException($this->errorToString($errors), 5);
                } else {
                    throw new UnprocessableEntityHttpException('Not validate Api Lead data', 5);
                }
            }
            $modelLead->checkIsSourceCode();
        } else {
            throw new BadRequestHttpException('Not found Lead data on POST request', 6);
        }

        $response = [];

        $lead = $this->leadCreateApiService->createByApi($modelLead, $this->apiProject);

        try {
            if (LeadCreatedEventData::checkByApiUser($this->apiUser)) {
                $job = new WebEngageLeadRequestJob($lead->id, WebEngageDictionary::EVENT_LEAD_CREATED);
                Yii::$app->queue_job->priority(100)->push($job);
            }
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'LeadController:v1:WebEngageLeadRequest');
        }

        if ((bool)(Yii::$app->params['settings']['api_create_lead_flight_product'] ?? false)) {
            $segments = [];
            /** @var LeadFlightSegment $flightSegment */
            foreach ($lead->getLeadFlightSegments()->orderBy(['departure' => SORT_ASC])->all() as $flightSegment) {
                $segments[] = new \modules\flight\src\dto\flightSegment\SegmentDTO(
                    null,
                    $flightSegment->origin,
                    $flightSegment->destination,
                    null,
                    null,
                    $flightSegment->origin_label,
                    $flightSegment->destination_label,
                    $flightSegment->departure
                );
            }
            $this->createProductFlightHandler->handle(
                $lead->id,
                $lead->cabin,
                (int)$lead->adults,
                (int)$lead->children,
                (int)$lead->infants,
                ...$segments
            );
        }

        $this->autoQuoteService->addAutoQuotesByJob($lead);

        $leadDataInserted = [];

        if (!empty($modelLead->lead_data)) {
            $leadDataService = new LeadDataCreateService();
            $leadDataService->createFromApi($modelLead->lead_data, $lead->id);
            $warnings = ArrayHelper::merge($warnings, $leadDataService->getErrors());
            $leadDataInserted = $leadDataService->getInserted();
        }

        if (!empty($modelLead->experiments) && is_array($modelLead->experiments)) {
            ExperimentTarget::saveExperimentList(Lead::class, $lead->id, $modelLead->experiments);
        }

        $clientDataInserted = [];
        if (!empty($modelLead->client_data) && ($clientId = $lead->client->id ?? null)) {
            [$clientDataInserted, $clientDataWarnings] = ClientDataService::createFromApi($modelLead->client_data, $clientId);
            if ($clientDataWarnings) {
                $warnings = ArrayHelper::merge($warnings, $clientDataWarnings);
            }
        }

//        $transaction = Yii::$app->db->beginTransaction();
//
//
//        $client = null;
//        $lead = new Lead();
//        $lead->scenario = Lead::SCENARIO_API;
//        $lead->l_type_create = Lead::TYPE_CREATE_API;
//
//        if($modelLead->phones) {
//            foreach ($modelLead->phones as $phone) {
//                $phone = trim($phone);
//                if(!$phone) {
//                    continue;
//                }
//
//                $lead->l_client_phone = $phone;
//
//                $phoneModel = ClientPhone::find()->where(['phone' => $phone])->orderBy(['id' => SORT_DESC])->limit(1)->one();
//
//                if($phoneModel && $phoneModel->client) {
//                    $client = $phoneModel->client;
//                    break;
//                }
//            }
//        }
//
//        if ($modelLead->emails) {
//            foreach ($modelLead->emails as $email) {
//
//                $email = mb_strtolower(trim($email));
//
//                if(!$email) {
//                    continue;
//                }
//                $lead->l_client_email = $email;
//            }
//        }
//
//
//        if(!$client) {
//            $client = new Client();
//
//            if ($modelLead->client_first_name) {
//                $client->first_name = $modelLead->client_first_name;
//            } else {
//                $client->first_name = 'ClientName';
//            }
//
//            if ($modelLead->client_last_name) {
//                $client->last_name = $modelLead->client_last_name;
//            }
//            if ($modelLead->client_middle_name) {
//                $client->middle_name = $modelLead->client_middle_name;
//            }
//        }
//
//
//
//        if (!$client->save()) {
//            throw new UnprocessableEntityHttpException($this->errorToString($client->errors));
//        }
//
//
//
//        //$lead->scenario = Lead::SCENARIO_API;
//        $lead->attributes = $modelLead->attributes;
//
//        $lead->client_id = $client->id;
//        if (!$lead->status) {
//            $lead->status = Lead::STATUS_PENDING;
//        }
//
//        if (!$lead->uid) {
//            $lead->uid = uniqid();
//        }
//
        ////
        ////        if ($modelLead->flights) {
        ////            $flightCount = count($modelLead->flights);
        ////
        ////            if ($flightCount === 1) {
        ////                $lead->trip_type = Lead::TRIP_TYPE_ONE_WAY;
        ////            } elseif ($flightCount === 2) {
        ////                $lead->trip_type = Lead::TRIP_TYPE_ROUND_TRIP;
        ////            } else {
        ////                $lead->trip_type = Lead::TRIP_TYPE_MULTI_DESTINATION;
        ////            }
        ////        }
//
//
//        if (!$lead->cabin) {
//            $lead->cabin = Lead::CABIN_ECONOMY;
//        }
//
//        if (!$lead->children) {
//            $lead->children = 0;
//        }
//        if (!$lead->infants) {
//            $lead->infants = 0;
//        }
//        if (!$lead->request_ip) {
//            $lead->request_ip = Yii::$app->request->remoteIP;
//        }
//
//        if ($this->apiProject) {
//            $lead->project_id = $this->apiProject->id;
//        }
//
//
//
//        if(!$lead->l_client_lang && $modelLead->user_language) {
//            $lead->l_client_lang = $modelLead->user_language;
//        }
//
//        if(!$lead->l_client_ua && $modelLead->user_agent) {
//            $lead->l_client_ua = $modelLead->user_agent;
//        }
//
//        if(!$lead->l_client_first_name && $modelLead->client_first_name) {
//            $lead->l_client_first_name = $modelLead->client_first_name;
//        }
//
//        if(!$lead->l_client_last_name && $modelLead->client_last_name) {
//            $lead->l_client_last_name = $modelLead->client_last_name;
//        }
//
//        $lead->l_call_status_id = Lead::CALL_STATUS_READY;
//
//        $request_hash = $this->leadHashGenerator->generate(
//            $modelLead->request_ip,
//            $modelLead->project_id,
//            $modelLead->adults,
//            $modelLead->children,
//            $modelLead->infants,
//            $modelLead->cabin,
//            $modelLead->phones,
//            $modelLead->flights
//        );
//
//        if ($duplicate = $this->leadRepository->getByRequestHash($request_hash)) {
//            $lead->duplicate($duplicate->id, $modelLead->employee_id, null);
//            Yii::info('Warning: detected duplicate Lead (Origin id: ' . $duplicate->id . ', Hash: ' . $request_hash . ')', 'nfo\API:Lead:duplicate');
//        }
//
//        if ($request_hash && $lead->isEmptyRequestHash()) {
//            $lead->setRequestHash($request_hash);
//        }
//
        ////        $request_hash = $modelLead->getRequestHash();
        ////
        ////        $duplicateLead = Lead::find()
        ////            ->where(['l_request_hash' => $request_hash])->andWhere(['>=', 'created', date('Y-m-d H:i:s', strtotime('-12 hours'))])
        ////            ->orderBy(['id' => SORT_ASC])->limit(1)->one();
        ////
        ////        if($duplicateLead) {
        ////            $lead->l_duplicate_lead_id = $duplicateLead->id;
        ////            $lead->status = Lead::STATUS_TRASH;
        ////            Yii::info('Warning: detected duplicate Lead (Origin id: '.$duplicateLead->id.', Hash: '.$request_hash.')', 'info\API:Lead:duplicate');
        ////        }
        ////
        ////
        ////        if(!$lead->l_request_hash && $request_hash) {
        ////            $lead->l_request_hash = $request_hash;
        ////        }
//
//
//
//
//
//        if (!$lead->validate()) {
//            if ($errors = $lead->getErrors()) {
//                throw new UnprocessableEntityHttpException($this->errorToString($errors), 7);
//            } else {
//                throw new UnprocessableEntityHttpException('Not validate Lead data', 7);
//            }
//        }
//
//        if (!$lead->save()) {
//            Yii::error(print_r($lead->errors, true), 'API:Lead:create:Lead:save');
//            $transaction->rollBack();
//            throw new UnprocessableEntityHttpException($this->errorToString($modelLead->errors), 8);
//        }
//
//        if (!$lead->trip_type) {
//            $lead->trip_type = Lead::TRIP_TYPE_ROUND_TRIP;
//        }
//
//        if ($modelLead->flights) {
//
//            $segmentsDTO = [];
//
//            foreach ($modelLead->flights as $flight) {
//                $flightModel = new LeadFlightSegment();
//                $flightModel->scenario = LeadFlightSegment::SCENARIO_CREATE_API;
//
//
//                $flightModel->lead_id = $lead->id;
//                $flightModel->origin = $flight['origin'];
//                $flightModel->destination = $flight['destination'];
//                $flightModel->departure = $flight['departure'];
//
//                $segmentsDTO[] = new SegmentDTO($flight['origin'], $flight['destination']);
//
//                if (!$flightModel->save()) {
//                    Yii::error(print_r($flightModel->errors, true), 'API:Lead:create:LeadFlightSegment:save');
//                    $transaction->rollBack();
//                    throw new UnprocessableEntityHttpException($this->errorToString($flightModel->errors), 10);
//                }
//            }
//
//            $lead->setTripType(LeadTripTypeCalculator::calculate(...$segmentsDTO));
//        }
//
//
//        if ($modelLead->emails) {
//            foreach ($modelLead->emails as $email) {
//
//                $email = mb_strtolower(trim($email));
//
//                $emailExist = ClientEmail::find()->where(['email' => $email, 'client_id' => $client->id])->exists();
//                if($emailExist) {
//                    continue;
//                }
//
//                $emailModel = new ClientEmail();
//
//                $emailModel->client_id = $client->id;
//                $emailModel->email = $email;
//                $emailModel->created = date('Y-m-d H:i:s');
//
//                if (!$emailModel->save()) {
//                    Yii::error(print_r($emailModel->errors, true), 'API:Lead:create:ClientEmail:save');
//                    $transaction->rollBack();
//                    throw new UnprocessableEntityHttpException($this->errorToString($emailModel->errors), 11);
//                }
//            }
//        }
//
//        if ($modelLead->phones) {
//            foreach ($modelLead->phones as $phone) {
//
//                $phone = trim($phone);
//
//                $phoneExist = ClientPhone::find()->where(['phone' => $phone, 'client_id' => $client->id])->exists();
//                if($phoneExist) {
//                    continue;
//                }
//
//                $phoneModel = new ClientPhone();
//
//                $phoneModel->client_id = $client->id;
//                $phoneModel->phone = $phone;
//                $phoneModel->created = date('Y-m-d H:i:s');
//
//                if (!$phoneModel->save()) {
//                    Yii::error(print_r($phoneModel->errors, true), 'API:Lead:create:ClientPhone:save');
//                    $transaction->rollBack();
//                    throw new UnprocessableEntityHttpException($this->errorToString($phoneModel->errors), 12);
//                }
//            }
//        }
//
//        $transaction->commit();


        try {
            $response['lead'] = $lead;
            $response['flights'] = $modelLead->flights;
            $response['emails'] = $modelLead->emails;
            $response['phones'] = $modelLead->phones;
            $response['client'] = [
                'uuid' => $lead->client->uuid,
                'client_id' => $lead->client_id,
                'first_name' => $lead->client->first_name,
                'middle_name' => $lead->client->middle_name,
                'last_name' => $lead->client->last_name,
                'phones' => $lead->client->getClientPhonesByType(
                    [
                        null,
                        ClientPhone::PHONE_VALID,
                        ClientPhone::PHONE_NOT_SET,
                        ClientPhone::PHONE_FAVORITE,
                    ]
                ),
                'emails' => $lead->client->getClientEmailsByType(
                    [
                        null,
                        ClientEmail::EMAIL_NOT_SET,
                        ClientEmail::EMAIL_FAVORITE,
                        ClientEmail::EMAIL_VALID,
                    ]
                ),
            ];

            $response['leadData'] = $leadDataInserted;
            $response['clientData'] = $clientDataInserted;
            $response['warnings'] = $warnings;
        } catch (\Throwable $e) {
//            $transaction->rollBack();
            Yii::error($e->getTraceAsString(), 'API:lead:create:try');
            if (Yii::$app->request->get('debug')) {
                $message = $e->getTraceAsString();
            } else {
                $message = $e->getMessage() . ' (code:' . $e->getCode() . ', line: ' . $e->getLine() . ')';
            }

            $response['error'] = $message;
            $response['error_code'] = 30;
        }


        if (isset($response['error']) && $response['error']) {
        } else {
            /*$responseData['status']     = 200;
            $responseData['name']       = 'Success';
            $responseData['code']       = 0;
            $responseData['message']    = '';*/

            $responseData['status'] = 'Success';
            if (!empty($modelLead->visitorLogErrors)) {
                $responseData['errors']['visitor_log'] = $modelLead->visitorLogErrors;
            } else {
                $responseData['errors'] = [];
            }
            $responseData['lead_id'] = $lead->id;
            $responseData['client_id'] = $lead->client_id;
        }

        $responseData['data']['response'] = $response;
        // $responseData['data']['request']                = $modelLead;

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
     *
     * @api {post} /v1/lead/update Update Lead
     * @apiVersion 0.1.0
     * @apiName UpdateLead
     * @apiGroup Leads
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization    Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string}           [apiKey]   API Key for Project (if not use Basic-Authorization)
     * @apiParam {object}           lead                                               Lead data array
     * @apiParam {int}                  lead.lead_id                                     Lead ID
     * @apiParam {int}                  lead.source_id                                     Source ID
     * @apiParam {int{1..9}}            lead.adults                                        Adult count
     * @apiParam {string{1}=E-ECONOMY,B-BUSINESS,F-FIRST,P-PREMIUM}        lead.cabin                                         Cabin
     * @apiParam {array[]}              lead.emails                                         Array of Emails (string)
     * @apiParam {array[]}              lead.phones                                         Array of Phones (string)
     * @apiParam {object[]}             lead.flights                                        Array of Flights
     * @apiParam {string{3}}                                lead.flights.origin                 Flight Origin location Airport IATA-code
     * @apiParam {string{3}}                                lead.flights.destination            Flight Destination location Airport IATA-code
     * @apiParam {datetime{YYYY-MM-DD HH:II:SS}}            lead.flights.departure              Flight Departure DateTime (format YYYY-MM-DD HH:ii:ss)
     * @apiParam {string{2}=OW-ONE_WAY,RT-ROUND_TRIP,MC-MULTI_DESTINATION}        [lead.trip_type]                                         Trip type (if empty - autocomplete)
     * @apiParam {int=1-PENDING,2-PROCESSING,4-REJECT,5-FOLLOW_UP,8-ON_HOLD,10-SOLD,11-TRASH,12-BOOKED,13-SNOOZE}        [lead.status]                                       Status
     *
     * @apiParam {int{0..9}}            [lead.children]                                      Children count
     * @apiParam {int{0..9}}            [lead.infants]                                       Infant count
     * @apiParam {string{40}}           [lead.uid]                                           UID value
     * @apiParam {text}                 [lead.notes_for_experts]                             Notes for expert
     * @apiParam {text}                 [lead.request_ip_detail]                             Request IP detail (autocomplete)
     * @apiParam {string{50}}           [lead.request_ip]                                    Request IP
     * @apiParam {int}                  [lead.snooze_for]                                    Snooze for
     * @apiParam {int}                  [lead.rating]                                        Rating
     *
     * @apiParam {string{3..100}}       [lead.client_first_name]                            Client first name
     * @apiParam {string{3..100}}       [lead.client_last_name]                             Client last name
     * @apiParam {string{3..100}}       [lead.client_middle_name]                           Client middle name
     *
     *
     *
     * @apiParamExample {json} Request-Example:
     * {
     *    "apiKey": "d190c378e131ccfd8a889c8ee8994cb55f22fbeeb93f9b99007e8e7ecc24d0dd",
     *    "lead": {
     *        "lead_id": 38,
     *        "flights": [
     *            {
     *                "origin": "KIV",
     *                "destination": "DME",
     *                "departure": "2018-10-13 13:50:00",
     *            },
     *            {
     *                "origin": "DME",
     *                "destination": "KIV",
     *                "departure": "2018-10-18 10:54:00",
     *            }
     *        ],
     *        "emails": [
     *          "email1@gmail.com",
     *          "email2@gmail.com",
     *        ],
     *        "phones": [
     *          "+373-69-487523",
     *          "022-45-7895-89",
     *        ],
     *        "source_id": 38,
     *        "adults": 1,
     *        "client_first_name": "Alexandr",
     *        "client_last_name": "Freeman"
     *    }
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
     *      HTTP/1.1 422 Unprocessable entity
     *      {
     *          "name": "Unprocessable entity",
     *          "message": "Flight [0]: Destination should contain at most 3 characters.",
     *          "code": 5,
     *          "status": 422,
     *          "type": "yii\\web\\UnprocessableEntityHttpException"
     *      }
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
        $modelLead = new ApiLead();
        $modelLead->scenario = ApiLead::SCENARIO_UPDATE;

        //print_r($this->apiProject); exit;

        if ($this->apiProject) {
            $modelLead->project_id = $this->apiProject->id;
        }

        if ($modelLead->load(Yii::$app->request->post())) {
            if (!$modelLead->validate()) {
                if ($errors = $modelLead->getErrors()) {
                    throw new UnprocessableEntityHttpException($this->errorToString($errors), 5);
                } else {
                    throw new UnprocessableEntityHttpException('Not validate Api Lead data', 5);
                }
            }
        } else {
            throw new BadRequestHttpException('Not found Lead data on POST request', 6);
        }

        $lead = Lead::findOne($modelLead->lead_id);
        //$lead->scenario = Lead::SCENARIO_UPDATE_API;

        if (!$lead) {
            throw new NotFoundHttpException('Not found lead ID: ' . $modelLead->lead_id, 9);
        }

        //return $lead->attributes;

        $response = [];

        $transaction = Yii::$app->db->beginTransaction();

        try {
            foreach ($modelLead->attributes as $attrKey => $attrValue) {
                if ($attrValue === null) {
                    continue;
                }
                if (isset($lead->$attrKey)) {
                    $lead->$attrKey = $attrValue;
                }
            }

            $client = $lead->client;

            if ($modelLead->client_first_name || $modelLead->client_last_name || $modelLead->client_middle_name) {
                if ($client) {
                    if ($modelLead->client_first_name) {
                        $client->first_name = $modelLead->client_first_name;
                    }
                    if ($modelLead->client_last_name) {
                        $client->last_name = $modelLead->client_last_name;
                    }
                    if ($modelLead->client_middle_name) {
                        $client->middle_name = $modelLead->client_middle_name;
                    }

                    $client->save();
                }
            }


            if (!$lead->validate()) {
                if ($errors = $lead->getErrors()) {
                    throw new UnprocessableEntityHttpException($this->errorToString($errors), 7);
                } else {
                    throw new UnprocessableEntityHttpException('Not validate Lead data', 7);
                }
            }

            if (!$lead->save()) {
                Yii::error(print_r($lead->errors, true), 'API:Lead:update:Lead:save');
                $transaction->rollBack();
                throw new UnprocessableEntityHttpException($this->errorToString($modelLead->errors), 8);
            }


            if ($modelLead->flights) {
                LeadFlightSegment::deleteAll(['lead_id' => $lead->id]);

                foreach ($modelLead->flights as $flight) {
                    $flightModel = new LeadFlightSegment();
                    $flightModel->scenario = LeadFlightSegment::SCENARIO_UPDATE_API;

                    $flightModel->lead_id = $lead->id;
                    $flightModel->origin = $flight['origin'];
                    $flightModel->destination = $flight['destination'];
                    $flightModel->departure = $flight['departure'];

                    if (!$flightModel->save()) {
                        Yii::error(print_r($flightModel->errors, true), 'API:Lead:update:LeadFlightSegment:save');
                        $transaction->rollBack();
                    }
                }
            }


            if ($modelLead->emails && $client) {
                ClientEmail::deleteAll(['client_id' => $client->id]);
                foreach ($modelLead->emails as $email) {
                    $emailModel = new ClientEmail();

                    $emailModel->client_id = $client->id;
                    $emailModel->email = $email;
                    $emailModel->created = date('Y-m-d H:i:s');

                    if (!$emailModel->save()) {
                        Yii::error(print_r($emailModel->errors, true), 'API:Lead:update:ClientEmail:save');
                        $transaction->rollBack();
                    }
                }
            }

            if ($modelLead->phones && $client) {
                ClientPhone::deleteAll(['client_id' => $client->id]);
                foreach ($modelLead->phones as $phone) {
                    $phoneModel = new ClientPhone();

                    $phoneModel->client_id = $client->id;
                    $phoneModel->phone = $phone;
                    $phoneModel->created = date('Y-m-d H:i:s');

                    if (!$phoneModel->save()) {
                        Yii::error(print_r($phoneModel->errors, true), 'API:Lead:update:ClientPhone:save');
                        $transaction->rollBack();
                    }
                }
            }

            //$transaction->commit();

            $response['lead'] = $lead;
            $response['flights'] = $lead->leadFlightSegments;
            $response['emails'] = $lead->client->clientEmails;
            $response['phones'] = $lead->client->clientPhones;
            $response['client'] = $lead->client;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error($e->getTraceAsString(), 'API:lead:update:try');
            if (Yii::$app->request->get('debug')) {
                $message = ($e->getTraceAsString());
            } else {
                $message = $e->getMessage() . ' (code:' . $e->getCode() . ', line: ' . $e->getLine() . ')';
            }

            $response['error'] = $message;
            $response['error_code'] = 30;
        }


        if (isset($response['error']) && $response['error']) {
        } else {
            $responseData['status'] = 200;
            $responseData['name'] = 'Success';
            $responseData['code'] = 0;
            $responseData['message'] = '';
        }


        $responseData['data']['response'] = $response;
        $responseData['data']['request'] = $modelLead;


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
     *
     * @api {post} /v1/lead/get Get Lead
     * @apiVersion 0.1.0
     * @apiName GetLead
     * @apiGroup Leads
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization    Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string}           [apiKey]   API Key for Project (if not use Basic-Authorization)
     * @apiParam {object}           lead                                               Lead data array
     * @apiParam {int}                  lead.lead_id                                   Lead ID
     * @apiParam {int}                  lead.source_id                                 Source ID
     * @apiParam {string}               lead.uid                                       Uid
     *
     * @apiParamExample {json} Request-Example:
     * {
     *    "apiKey": "d190c378e131ccfd8a889c8ee8994cb55f22fbeeb93f9b99007e8e7ecc24d0dd",
     *    "lead": {
     *        "lead_id": 302,
     *        "source_id": 38,
     *        "uid": "5fe2081025a25"
     *    }
     * }
     *
     * @apiSuccess {Integer} response_id    Response Id
     * @apiSuccess {DateTime} request_dt    Request Date & Time
     * @apiSuccess {DateTime} response_dt   Response Date & Time
     * @apiSuccess {Array} data Data Array
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
            "status": 200,
            "name": "Success",
            "code": 0,
            "message": "",
            "data": {
                "response": {
                    "lead": {
                        "id": 371058,
                        "client_id": 333094,
                        "employee_id": 501,
                        "status": 2,
                        "uid": "61234c87a90ee",
                        "project_id": 2,
                        "source_id": 18,
                        "trip_type": "RT",
                        "cabin": "E",
                        "adults": 1,
                        "children": 0,
                        "infants": 0,
                        "notes_for_experts": null,
                        "created": "2021-08-23 07:21:43",
                        "updated": "2021-08-23 07:22:24",
                        "request_ip": null,
                        "request_ip_detail": null,
                        "offset_gmt": null,
                        "snooze_for": null,
                        "rating": 0,
                        "called_expert": 0,
                        "discount_id": null,
                        "bo_flight_id": null,
                        "additional_information": null,
                        "l_answered": 0,
                        "clone_id": null,
                        "description": null,
                        "final_profit": null,
                        "tips": "0.00",
                        "gid": "4da708ecb49cdf2f0ccffacd5f0afeeb",
                        "agents_processing_fee": 70,
                        "l_call_status_id": 0,
                        "l_pending_delay_dt": null,
                        "l_client_first_name": "Test",
                        "l_client_last_name": "",
                        "l_client_phone": "+12015550123",
                        "l_client_email": "xxx@gmail.com",
                        "l_client_lang": null,
                        "l_client_ua": null,
                        "l_request_hash": "5c2d61ef547d4318f3befd6f62662433",
                        "l_duplicate_lead_id": null,
                        "l_init_price": null,
                        "l_last_action_dt": "2021-08-24 09:06:50",
                        "l_dep_id": 1,
                        "l_delayed_charge": null,
                        "l_type_create": 1,
                        "l_is_test": 0,
                        "hybrid_uid": null,
                        "l_visitor_log_id": 28,
                        "l_status_dt": "2021-08-23 07:21:43",
                        "l_expiration_dt": null,
                        "l_type": null
                    },
                    "flights": [
                        {
                            "id": 698035,
                            "lead_id": 371058,
                            "origin": "YWK",
                            "destination": "YZV",
                            "departure": "2021-11-01",
                            "created": "2021-08-23 07:22:24",
                            "updated": "2021-08-23 07:23:18",
                            "flexibility": 0,
                            "flexibility_type": "-",
                            "origin_label": null,
                            "destination_label": null
                        },
                        {
                            "id": 698036,
                            "lead_id": 371058,
                            "origin": "YZV",
                            "destination": "YWK",
                            "departure": "2021-11-06",
                            "created": "2021-08-23 07:22:24",
                            "updated": "2021-08-23 07:23:18",
                            "flexibility": 0,
                            "flexibility_type": "-",
                            "origin_label": null,
                            "destination_label": null
                        }
                    ],
                    "emails": [
                        {
                            "id": 130813,
                            "client_id": 333094,
                            "email": "xxx@gmail.com",
                            "created": "2021-08-23 07:21:43",
                            "updated": "2021-08-23 07:21:43",
                            "comments": null,
                            "type": null,
                            "ce_title": null
                        }
                    ],
                    "phones": [
                        {
                            "id": 342561,
                            "client_id": 333094,
                            "phone": "+12012345678",
                            "created": "2021-05-04 06:01:34",
                            "updated": "2021-05-04 06:01:34",
                            "comments": null,
                            "is_sms": 0,
                            "validate_dt": null,
                            "type": null,
                            "cp_title": null,
                            "cp_cpl_uid": null
                        }
                    ],
                    "client": {
                        "id": 333094,
                        "first_name": "Bilbo",
                        "middle_name": "Underhill",
                        "last_name": "Baggins",
                        "created": "2021-05-04 06:01:34",
                        "updated": "2021-05-04 06:01:34",
                        "uuid": "0cbe8947-0b91-4d25-a154-f85d773a3998",
                        "parent_id": 70135,
                        "is_company": 0,
                        "is_public": 0,
                        "company_name": null,
                        "description": null,
                        "disabled": 0,
                        "rating": null,
                        "cl_type_id": 1,
                        "cl_type_create": 2,
                        "cl_project_id": 2,
                        "cl_ca_id": null,
                        "cl_ppn": null,
                        "cl_excluded": 0,
                        "cl_ip": null,
                        "cl_locale": null,
                        "cl_marketing_country": null,
                        "cl_call_recording_disabled": 0
                    },
                    "experiment_ids": [
                        123,
                        124
                   ],
                   "lead_preferences": {
                            "id": 155398,
                            "lead_id": 371058,
                            "notes": null,
                            "pref_language": null,
                            "pref_currency": "USD",
                            "pref_airline": null,
                            "number_stops": null,
                            "clients_budget": null,
                            "market_price": null
                   },
                   "lead_data": [
                        {
                            "key": "cross_system_xp",
                            "value": "example123"
                        }
                   ]
                }
            },
            "action": "v1/lead/get",
            "response_id": 8,
            "request_dt": "2021-09-15 07:38:09",
            "response_dt": "2021-09-15 07:38:09",
            "execution_time": 0.039,
            "memory_usage": 637944
        }
     *
     * @apiError UserNotFound The id of the User was not found.
     *
     * @apiErrorExample Error-Response:
     *      HTTP/1.1 404 Not Found
     *      {
     *          "name": "Not Found",
     *          "message": "Not found lead ID: 302",
     *          "code": 9,
     *          "status": 404,
     *          "type": "yii\\web\\NotFoundHttpException"
     *      }
     *
     *
     * @return mixed
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     */

    public function actionGet()
    {
        $this->checkPost();
        $this->startApiLog($this->action->uniqueId);

        $modelLead = new ApiLead();
        $modelLead->scenario = ApiLead::SCENARIO_GET;

        if ($this->apiProject) {
            $modelLead->project_id = $this->apiProject->id;
        }

        if ($modelLead->load(Yii::$app->request->post())) {
            if (!$modelLead->validate()) {
                if ($errors = $modelLead->getErrors()) {
                    throw new UnprocessableEntityHttpException($this->errorToString($errors), 5);
                } else {
                    throw new UnprocessableEntityHttpException('Not validate Api Lead data', 5);
                }
            }
        } else {
            throw new BadRequestHttpException('Not found Lead data on POST request', 6);
        }

        $lead = Lead::find()->where([
            'uid' => $modelLead->uid,
            'source_id' => $modelLead->source_id
        ])->one();
        if (!$lead) {
            throw new NotFoundHttpException('Not found lead ID: ' . $modelLead->lead_id, 9);
        }

        $response = [];

        try {
            $response['lead'] = $lead;
            $response['experiment_ids'] = $modelLead->experimentsIds;
            $response['flights'] = $lead->leadFlightSegments;
            $response['emails'] = $lead->client->clientEmails;
            $response['phones'] = $lead->client->clientPhones;
            $response['client'] = $lead->client;
            $response['lead_preferences'] = $lead->leadPreferences ?? null;
            ArrayHelper::setValue(
                $response,
                'lead_data',
                LeadDataService::getByLeadFields($lead)
            );
        } catch (\Throwable $e) {
            Yii::error($e->getTraceAsString(), 'API:lead:get:try');
            if (Yii::$app->request->get('debug')) {
                $message = ($e->getTraceAsString());
            } else {
                $message = $e->getMessage() . ' (code:' . $e->getCode() . ', line: ' . $e->getLine() . ')';
            }

            $response['error'] = $message;
            $response['error_code'] = 30;
        }

        if (isset($response['error']) && $response['error']) {
        } else {
            $responseData['status'] = 200;
            $responseData['name'] = 'Success';
            $responseData['code'] = 0;
            $responseData['message'] = '';
        }

        $responseData['data']['response'] = $response;
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
     * @api {post} /v1/lead/call-expert Update Lead Call Expert
     * @apiVersion 0.1.0
     * @apiName UpdateLeadCallExpert
     * @apiGroup Leads
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization    Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {object}           call                                        CallExpert data array
     * @apiParam {int}                  call.lce_id                             Call Expert ID
     * @apiParam {int=1-PENDING,2-PROCESSING,3-DONE,4-CANCEL}                  call.lce_status_id                    Status Id
     * @apiParam {text}                 call.lce_response_text                  Response text from Expert (Required on lce_status_id = 3)
     * @apiParam {string{30}}           call.lce_expert_username                Expert Username (Required on lce_status_id = 3)
     * @apiParam {int}                  [call.lce_expert_user_id]               Expert Id
     * @apiParam {array[]}              [call.lce_response_lead_quotes]         Array of UID quotes (string)
     *
     *
     *
     * @apiParamExample {json} Request-Example:
     * {
     *    "call": {
     *        "lce_id": 38,
     *        "lce_response_text": "Message from expert",
     *        "lce_expert_username": "Alex",
     *        "lce_expert_user_id": 12,
     *        "lce_response_lead_quotes": [
     *              "5ccbe7a458765",
     *              "5ccbe797a6a22"
     *          ],
     *        "lce_status_id": 2
     *    }
     * }
     *
     * @apiSuccess {String}     status          Response Status
     * @apiSuccess {String}     name            Response Name
     * @apiSuccess {Integer}    code            Response Code
     * @apiSuccess {String}     message         Response Message
     * @apiSuccess {Array}      data            Response Data Array
     * @apiSuccess {String}     action          Response API action
     * @apiSuccess {Integer}    response_id     Response Id
     * @apiSuccess {DateTime}   request_dt      Request Date & Time
     * @apiSuccess {DateTime}   response_dt     Response Date & Time
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *  "status": 200,
     *  "name": "Success",
     *  "code": 0,
     *  "message": "",
     *  "data": {
     *      "response": {
     *          "lce_id": 8,
     *          "lce_lead_id": 113947,
     *          "lce_request_text": "12\r\n2\r\nqwe qwe qwe qwe qwe fasd asd fasdf\r\n",
     *          "lce_request_dt": "2019-05-03 14:08:20",
     *          "lce_response_text": "Test expert text",
     *          "lce_response_lead_quotes": "[\"5ccbe7a458765\", \"5ccbe797a6a22\"]",
     *          "lce_response_dt": "2019-05-07 09:14:01",
     *          "lce_status_id": 3,
     *          "lce_agent_user_id": 167,
     *          "lce_expert_user_id": "2",
     *          "lce_expert_username": "Alex",
     *          "lce_updated_dt": "2019-05-07 09:14:01"
     *      }
     *  },
     *  "action": "v1/lead/call-expert",
     *  "response_id": 457671,
     *  "request_dt": "2019-05-07 09:14:01",
     *  "response_dt": "2019-05-07 09:14:01"
     * }
     *
     * @apiError UserNotFound The id of the User was not found.
     *
     * @apiErrorExample Error-Response:
     *
     *
     * HTTP/1.1 401 Unauthorized
     *  {
     *      "name": "Unauthorized",
     *      "message": "Your request was made with invalid credentials.",
     *      "code": 0,
     *      "status": 401,
     *      "type": "yii\\web\\UnauthorizedHttpException"
     *  }
     *
     *
     * HTTP/1.1 400 Bad Request
     *  {
     *      "name": "Bad Request",
     *      "message": "Not found LeadCallExpert data on POST request",
     *      "code": 6,
     *      "status": 400,
     *      "type": "yii\\web\\BadRequestHttpException"
     *  }
     *
     *
     * HTTP/1.1 404 Not Found
     *  {
     *      "name": "Not Found",
     *      "message": "Not found LeadCallExpert ID: 100",
     *      "code": 9,
     *      "status": 404,
     *      "type": "yii\\web\\NotFoundHttpException"
     *  }
     *
     *
     * HTTP/1.1 422 Unprocessable entity
     *  {
     *      "name": "Unprocessable entity",
     *      "message": "Response Text cannot be blank.; Expert Username cannot be blank.",
     *      "code": 5,
     *      "status": 422,
     *      "type": "yii\\web\\UnprocessableEntityHttpException"
     *  }
     *
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     */

    public function actionCallExpert(): array
    {
        $this->checkPost();
        $this->startApiLog($this->action->uniqueId);
        $response = [];

        $model = new ApiLeadCallExpert();

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->validate()) {
                if ($errors = $model->getErrors()) {
                    throw new UnprocessableEntityHttpException($this->errorToString($errors), 5);
                }

                throw new UnprocessableEntityHttpException('Not validate LeadCallExpert data', 5);
            }
        } else {
            throw new BadRequestHttpException('Not found LeadCallExpert data on POST request', 6);
        }

        $leadCallExpert = LeadCallExpert::findOne($model->lce_id);
        $leadCallExpert->scenario = LeadCallExpert::SCENARIO_API_UPDATE;

        if (!$leadCallExpert) {
            throw new NotFoundHttpException('Not found LeadCallExpert ID: ' . $model->lce_id, 9);
        }

        $leadCallExpert->lce_response_dt = date('Y-m-d H:i:s');
        $leadCallExpert->lce_updated_dt = date('Y-m-d H:i:s');

        if ($model->lce_response_lead_quotes && is_array($model->lce_response_lead_quotes)) {
            $leadCallExpert->lce_response_lead_quotes = json_encode($model->lce_response_lead_quotes);
        }

        if ($model->lce_response_text) {
            $leadCallExpert->lce_response_text = $model->lce_response_text;
        }

        if ($model->lce_expert_username) {
            $leadCallExpert->lce_expert_username = $model->lce_expert_username;
        }

        if ($model->lce_expert_user_id) {
            $leadCallExpert->lce_expert_user_id = $model->lce_expert_user_id;
        }

        $leadCallExpert->lce_status_id = $model->lce_status_id;

        if ($leadCallExpert->save()) {
            if ($leadCallExpert->lce_agent_user_id) {
                if (
                    $ntf = Notifications::create(
                        $leadCallExpert->lce_agent_user_id,
                        'Expert Response',
                        'Expert (' . Html::encode($leadCallExpert->lce_expert_username) . ') Response (' . $leadCallExpert->getStatusName() . '). Lead (Id: ' . ($leadCallExpert->lceLead ? Purifier::createLeadShortLink($leadCallExpert->lceLead) : 'nof found') . ')',
                        Notifications::TYPE_INFO,
                        true
                    )
                ) {
                    // Notifications::socket($leadCallExpert->lce_agent_user_id, $leadCallExpert->lce_lead_id, 'getNewNotification', [], true);
                    $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                    Notifications::publish('getNewNotification', ['user_id' => $leadCallExpert->lce_agent_user_id], $dataNotification);
                }
            }

            $response = $leadCallExpert->attributes;

            $responseData['status']     = 200;
            $responseData['name']       = 'Success';
            $responseData['code']       = 0;
            $responseData['message']    = '';
        } else {
            $response['error'] = $this->errorToString($leadCallExpert->errors); //VarDumper::dumpAsString($leadCallExpert->errors, 10);
            $response['error_code'] = 10;
        }

        $responseData['data']['response'] = $response;
        //$responseData['data']['request'] = $model;

        $responseData = $this->apiLog->endApiLog($responseData);

        if (isset($response['error']) && $response['error']) {
            $error_code = $response['error_code'] ?? 0;
            throw new UnprocessableEntityHttpException($response['error'], $error_code);
        }

        return $responseData;
    }


    //apiDoc was missing and recreated briefly todo double check carefully
    /**
     *
     * @api {post} v1/lead/sold-update Update Sold Lead
     * @apiVersion 0.1.0
     * @apiName SoldUpdate
     * @apiGroup Leads
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization    Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string}           [apiKey]   API Key for Project (if not use Basic-Authorization)
     * @apiParam {object}           lead                                               Lead data array
     * @apiParam {int}                  lead.source_id                                 Source ID
     * @apiParam {string}               lead.uid                                       Uid
     *
     * @apiParamExample {json} Request-Example:
     * {
     *      "apiKey": "YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Lead":
     *      {
     *          "uid": "62b2d69124dd1",
     *          "market_info_id": 32,
     *          "bo_flight_id": 32,
     *          "final_profit": -46.02,
     *          "tips": 0,
     *          "additional_information": [
     *          {
     *              "pnr": "GEQGANQ",
     *              "bo_sale_id": 32,
     *              "vtf_processed": false,
     *              "tkt_processed": true,
     *              "exp_processed": true,
     *              "bkk_processed": false,
     *              "passengers": ["Smith/Thomas (ADT)"],
     *              "paxInfo": [
     *              {
     *                  "pax": "Smith/Thomas (ADT)",
     *                  "dob": "1900-02-12",
     *                  "sex": "M"
     *              }]
     *          }],
     *          "status": 10
     *       },
     *       "received_microtime": 1655892930.0878
     * }
     *
     * @apiSuccess {Integer} response_id    Response Id
     * @apiSuccess {DateTime} request_dt    Request Date & Time
     * @apiSuccess {DateTime} response_dt   Response Date & Time
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *        "status": "Success",
     *        "errors": [],
     *        "action": "v1/lead/sold-update",
     *        "response_id": 75,
     *        "request_dt": "2022-06-23 12:39:24",
     *        "response_dt": "2022-06-23 12:39:24",
     *        "execution_time": 0.101,
     *        "memory_usage": 1215976
     *        }
     *
     *
     * @apiError UserNotFound The id of the User was not found.
     *
     * @apiErrorExample Error-Response:
     *      HTTP/1.1 404 Not Found
     *      {
     *          "name": "Not Found",
     *          "message": "Not found Lead UID: WD6q51209s9879",
     *          "code": 2,
     *          "status": 404,
     *          "type": "yii\\web\\NotFoundHttpException"
     *      }
     *
     *      HTTP/1.1 200 OK
     *      {
     *        "status": "Failed",
     *        "errors": ["This request with params has already been sent. Lead UID: 62668a051c07c"],
     *        "action": "v1/lead/sold-update",
     *        "response_id": 316,
     *        "request_dt": "2022-06-23 12:39:24",
     *        "response_dt": "2022-06-23 12:39:24",
     *        "execution_time": 0.101,
     *        "memory_usage": 1215976
     *        }
     *
     *
     *
     * @return mixed
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     */

    public function actionSoldUpdate()
    {
        $this->checkPost();
        $this->startApiLog($this->action->uniqueId);

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

        $idKey = 'action_sold_update_' . HashHelper::generateHashFromArray(Yii::$app->request->post());

        $response = [
            'status' => 'Failed',
            'errors' => []
        ];

        if (RedisHelper::checkDuplicate($idKey, 5)) {
            $response['errors'][] = 'This request with params has already been sent. Lead UID: ' . $leadAttributes['uid'];
            $responseData = $response;
            return $this->apiLog->endApiLog($responseData);
        }

        try {
            $isSold = $lead->isSold();
            $isReject = $lead->isReject();
            $lastStatus = $lead->status;

            $lead->attributes = $leadAttributes;

            if (!$lead->validate()) {
                $response['errors'][] = $lead->getErrors();
            } else {
                $result = $this->transactionManager->wrap(function () use ($lead, $leadAttributes, $isSold, $isReject, $lastStatus) {
                    $response = [];

                    if (!$isSold && $lead->isSold()) {
                        $lead->status = $lastStatus;
                        $lead->sold($lead->employee_id, null);
                    } elseif (!$isReject && $lead->isReject()) {
                        $lead->status = $lastStatus;
                        $lead->reject($lead->employee_id, null, 'BO rejected');
                    }
//                    elseif (!isset($leadAttributes['status'])) {
//                        Yii::warning('Lead: ' . $lead->id . ' Void Status', 'API:LeadSoldUpdate:Status');
//                    }

                    $this->leadRepository->save($lead);

                    if (
                        !empty($leadAttributes['additional_information']) &&
                        !empty($leadAttributes['additional_information'][0]['pnr'])
                    ) {
                        $aplliend = $lead->getAppliedAlternativeQuotes();
                        if ($aplliend !== null) {
                            $aplliend->record_locator = $leadAttributes['additional_information'][0]['pnr'];
                            $aplliend->save(false);
                            if ($aplliend->hasErrors()) {
                                $response['errors'] = $aplliend->getErrors();
                            }
                        }

                        $notifMessage = '';
                        $leadAdditionalInformation = $lead->additionalInformationFormFirstElement;
                        $leadOldAdditionalInformation = $lead->oldAdditionalInformationFormFirstElement;
                        foreach ($leadAttributes['additional_information'] as $additionalInformation) {
                            if (
                                isset($additionalInformation['tkt_processed']) && (bool)$additionalInformation['tkt_processed'] === false
                                && $leadAdditionalInformation->tkt_processed !== $leadOldAdditionalInformation->tkt_processed
                            ) {
                                $linkToLead = Purifier::createLeadShortLink($lead);
                                $notifMessage .= 'Flight ticket (PNR: ' . $additionalInformation['pnr'] . ') has been voided. Lead UID - ' . $linkToLead . PHP_EOL;
                            }
                        }

                        if ($notifMessage) {
                            Notifications::createAndPublish(
                                $lead->employee_id,
                                'Flight ticket has been voided',
                                $notifMessage,
                                Notifications::TYPE_INFO,
                                true
                            );
                        }
                    }

                    $lead->sendNotifOnProcessingStatusChanged();

//                    if (!empty($leadAttributes['info_tickets'])) {
//                        $result = $lead->sendSoldEmail($leadAttributes['info_tickets']);
//                        if (!$result['status']) {
//                            $response['errors'][] = $result['errors'];
//                            $transaction->rollBack();
//                        }
//                    }

                    if (empty($response['errors'])) {
                        $response['status'] = 'Success';
                    }

                    return $response;
                });

                if (isset($result['status'])) {
                    $response['status'] = $result['status'];
                }

                if (isset($result['errors'])) {
                    $response['errors'][] = $result['errors'];
                }
            }
        } catch (\Throwable $e) {
            Yii::error($e->getTraceAsString(), 'API:LeadSoldUpdate:try');
            if (Yii::$app->request->get('debug')) {
                $message = ($e->getTraceAsString());
            } else {
                $message = $e->getMessage() . ' (code:' . $e->getCode() . ', line: ' . $e->getLine() . ')';
            }
            $response['error'] = $message;
            $response['error_code'] = 30;
        }


//        $transaction = Yii::$app->db->beginTransaction();
//        try {
//
//            $lead->attributes = $leadAttributes;
//            if (!$lead->save()) {
//                $response['errors'][] = $lead->getErrors();
//                $transaction->rollBack();
//            } else {
//
//                if (!empty($leadAttributes['additional_information']) &&
//                    !empty($leadAttributes['additional_information']['pnr'])
//                ) {
//                    $aplliend = $lead->getAppliedAlternativeQuotes();
//                    if ($aplliend !== null) {
//                        $aplliend->record_locator = $leadAttributes['additional_information']['pnr'];
//                        $aplliend->save(false);
//                        if ($aplliend->hasErrors()) {
//                            $response['errors'][] = $aplliend->getErrors();
//                        }
//                    }
//                }
//
//                if (!empty($leadAttributes['info_tickets'])) {
//                    $result = $lead->sendSoldEmail($leadAttributes['info_tickets']);
//                    if (!$result['status']) {
//                        $response['errors'][] = $result['errors'];
//                        $transaction->rollBack();
//                    }
//                }
//
//                if (empty($response['errors'])) {
//                    $response['status'] = 'Success';
//                    $transaction->commit();
//                }
//            }
//
//        } catch (\Throwable $e) {
//
//            Yii::error($e->getTraceAsString(), 'API:Quote:create:try');
//            if (Yii::$app->request->get('debug')) $message = ($e->getTraceAsString());
//            else $message = $e->getMessage() . ' (code:' . $e->getCode() . ', line: ' . $e->getLine() . ')';
//
//            $response['error'] = $message;
//            $response['errors'] = $message;
//            $response['error_code'] = 30;
//
//            $transaction->rollBack();
//        }

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
}

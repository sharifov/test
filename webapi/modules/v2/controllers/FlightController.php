<?php

namespace webapi\modules\v2\controllers;

use common\components\jobs\ReprotectionCreateJob;
use common\helpers\LogHelper;
use DomainException;
use modules\flight\models\FlightRequest;
use modules\flight\src\repositories\flightRequest\FlightRequestRepository;
use modules\flight\src\useCases\api\exchangeExpired\ExchangeExpiredJob;
use modules\flight\src\useCases\api\productQuoteGet\ProductQuoteGetForm;
use modules\flight\src\useCases\flightQuote\createManually\helpers\FlightQuotePaxPriceHelper;
use modules\flight\src\useCases\reprotectionCreate\form\ReprotectionCreateForm;
use modules\flight\src\useCases\reprotectionCreate\form\ReprotectionGetForm;
use modules\flight\src\useCases\reprotectionExchange\form\ReProtectionExchangeForm;
use modules\flight\src\useCases\reprotectionExchange\service\ReProtectionExchangeService;
use modules\flight\src\useCases\voluntaryExchange\service\VoluntaryExchangeObjectCollection;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteChange\service\ProductQuoteChangeService;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use src\helpers\app\AppHelper;
use src\helpers\app\HttpStatusCodeHelper;
use src\helpers\product\ProductQuoteHelper;
use src\helpers\setting\SettingHelper;
use src\repositories\NotFoundException;
use src\repositories\product\ProductQuoteRepository;
use src\services\TransactionManager;
use webapi\src\ApiCodeException;
use webapi\src\logger\ApiLogger;
use webapi\src\logger\behaviors\SimpleLoggerBehavior;
use webapi\src\logger\behaviors\TechnicalInfoBehavior;
use webapi\src\Messages;
use webapi\src\response\behaviors\RequestBehavior;
use webapi\src\response\behaviors\ResponseStatusCodeBehavior;
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
use modules\flight\src\useCases\reprotectionDecision;

/**
 * Class FlightController
 *
 * @property TransactionManager $transactionManager
 * @property-read ProductQuoteRepository $productQuoteRepository
 * @property ReProtectionExchangeService $reProtectionExchangeService
 */
class FlightController extends BaseController
{
    private TransactionManager $transactionManager;
    /**
     * @var ProductQuoteRepository
     */
    private ProductQuoteRepository $productQuoteRepository;
    private ReProtectionExchangeService $reProtectionExchangeService;
    private VoluntaryExchangeObjectCollection $objectCollection;

    /**
     * @param $id
     * @param $module
     * @param ApiLogger $logger
     * @param TransactionManager $transactionManager
     * @param ProductQuoteRepository $productQuoteRepository
     * @param ReProtectionExchangeService $reProtectionExchangeService
     * @param VoluntaryExchangeObjectCollection $objectCollection
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        ApiLogger $logger,
        TransactionManager $transactionManager,
        ProductQuoteRepository $productQuoteRepository,
        ReProtectionExchangeService $reProtectionExchangeService,
        VoluntaryExchangeObjectCollection $objectCollection,
        $config = []
    ) {
        $this->transactionManager = $transactionManager;
        $this->productQuoteRepository = $productQuoteRepository;
        $this->reProtectionExchangeService = $reProtectionExchangeService;
        $this->objectCollection = $objectCollection;
        parent::__construct($id, $module, $logger, $config);
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['logger'] = [
            'class' => SimpleLoggerBehavior::class,
            'except' => [
//                'product-quote-get',
            ],
        ];
        $behaviors['request'] = [
            'class' => RequestBehavior::class,
            'except' => [
                'product-quote-get',
            ],
        ];
        $behaviors['responseStatusCode'] = [
            'class' => ResponseStatusCodeBehavior::class,
            'except' => [
                'product-quote-get',
            ],
        ];
        $behaviors['technical'] = [
            'class' => TechnicalInfoBehavior::class,
            'except' => [
                'product-quote-get',
            ],
        ];
        return $behaviors;
    }

    protected function verbs(): array
    {
        return ArrayHelper::merge(
            parent::verbs(),
            [
                'reprotection-get' => ['GET'],
                'product-quote-get' => ['GET'],
            ]
        );
    }

    /**
     * @api {post} /v2/flight/reprotection-create ReProtection Create
     * @apiVersion 0.1.0
     * @apiName ReProtection Create
     * @apiGroup Flight
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{10}}           booking_id                              Booking Id
     * @apiParam {string{50}}           project_key                             Project key
     * @apiParam {bool}                 [is_automate]                           Is automate (default false)
     * @apiParam {bool}                 [refundAllowed]                         Refund Allowed (default true)
     * @apiParam {object}               [flight_quote]                          Flight quote
     * @apiParam {string{2}}            flight_quote.gds                        Gds
     * @apiParam {string{10}}           flight_quote.pcc                        Pcc
     * @apiParam {string{50}}           flight_quote.fareType                   ValidatingCarrier
     * @apiParam {object}               flight_quote.trips                      Trips
     * @apiParam {int}                  [flight_quote.trips.duration]           Trip Duration
     * @apiParam {object}               flight_quote.trips.segments             Segments
     * @apiParam {string{format Y-m-d H:i}}    flight_quote.trips.segments.departureTime            DepartureTime
     * @apiParam {string{format Y-m-d H:i}}    flight_quote.trips.segments.arrivalTime              ArrivalTime
     * @apiParam {string{3}}                   flight_quote.trips.segments.departureAirportCode     Departure Airport Code IATA
     * @apiParam {string{3}}                   flight_quote.trips.segments.arrivalAirportCode       Arrival Airport Code IATA
     * @apiParam {int}                         [flight_quote.trips.segments.flightNumber]           Flight Number
     * @apiParam {string{1}}                   [flight_quote.trips.segments.bookingClass]           BookingClass
     * @apiParam {int}                         [flight_quote.trips.segments.duration]               Segment duration
     * @apiParam {string{3}}                   [flight_quote.trips.segments.departureAirportTerminal]     Departure Airport Terminal Code
     * @apiParam {string{3}}                   [flight_quote.trips.segments.arrivalAirportTerminal]       Arrival Airport Terminal Code
     * @apiParam {string{2}}                   [flight_quote.trips.segments.operatingAirline]       Operating Airline
     * @apiParam {string{2}}                   [flight_quote.trips.segments.marketingAirline]       Marketing Airline
     * @apiParam {string{30}}                  [flight_quote.trips.segments.airEquipType]          AirEquipType
     * @apiParam {string{3}}                   [flight_quote.trips.segments.marriageGroup]          MarriageGroup
     * @apiParam {int}                         [flight_quote.trips.segments.mileage]                Mileage
     * @apiParam {string{2}}                   [flight_quote.trips.segments.meal]                   Meal
     * @apiParam {string{50}}                  [flight_quote.trips.segments.fareCode]               Fare Code
     *
     * @apiParamExample {json} Request-Example:
     *   {
     *      "booking_id": "XXXYYYZ",
     *      "is_automate": false,
     *      "refundAllowed": true,
     *      "project_key":"ovago",
     *      "flight_quote":{
                "gds": "S",
                "pcc": "8KI0",
                "validatingCarrier": "PR",
                "fareType": "SR",
                "itineraryDump":[
                    "DL8727E 10JUN ROBCDG TK  930P  730A+ 11JUN TH/FR",
                    "DL8395E 11JUN CDGLAX HK 1015A 1255P FR",
                    "DL3580E 11JUN LAXSMF HK  546P  714P FR",
                    "DL3864E 10SEP SMFSEA TK 1027A 1234P FR",
                    "DL 759E 10SEP SEAMSP TK  813A  134P FR",
                    "DL  42E 10SEP MSPCDG TK  445P  815A+ 11SEP FR/SA",
                    "DL7351E 11SEP CDGROB HK 1015A  450P SA",
                    "DL7351E 11SEP BKOROB HK  320P  450P SA"
                ],
                "trips":[
                    {
                        "duration":848,
                        "segments":[
                            {
                                "departureTime":"2021-06-10 21:30",
                                "arrivalTime":"2021-06-11 07:30",
                                "flightNumber":"8727",
                                "bookingClass":"E",
                                "stop":0,
                                "stops":[

                                ],
                                "duration":600,
                                "departureAirportCode":"ROB",
                                "departureAirportTerminal":null,
                                "arrivalAirportCode":"CDG",
                                "arrivalAirportTerminal":null,
                                "operatingAirline":null,
                                "airEquipType":null,
                                "marketingAirline":"DL",
                                "marriageGroup":"",
                                "mileage":null,
                                "cabin":"Y",
                                "meal":null,
                                "fareCode":null,
                                "baggage":[

                                ],
                                "brandId":null
                            },
                            {
                                "departureTime":"2021-06-11 10:15",
                                "arrivalTime":"2021-06-11 12:55",
                                "flightNumber":"8395",
                                "bookingClass":"E",
                                "stop":0,
                                "stops":[

                                ],
                                "duration":160,
                                "departureAirportCode":"CDG",
                                "departureAirportTerminal":null,
                                "arrivalAirportCode":"LAX",
                                "arrivalAirportTerminal":null,
                                "operatingAirline":null,
                                "airEquipType":null,
                                "marketingAirline":"DL",
                                "marriageGroup":"",
                                "mileage":null,
                                "cabin":"Y",
                                "meal":null,
                                "fareCode":null,
                                "baggage":[

                                ],
                                "brandId":null
                            },
                            {
                                "departureTime":"2021-06-11 17:46",
                                "arrivalTime":"2021-06-11 19:14",
                                "flightNumber":"3580",
                                "bookingClass":"E",
                                "stop":0,
                                "stops":[

                                ],
                                "duration":88,
                                "departureAirportCode":"LAX",
                                "departureAirportTerminal":null,
                                "arrivalAirportCode":"SMF",
                                "arrivalAirportTerminal":null,
                                "operatingAirline":null,
                                "airEquipType":null,
                                "marketingAirline":"DL",
                                "marriageGroup":"",
                                "mileage":null,
                                "cabin":"Y",
                                "meal":null,
                                "fareCode":null,
                                "baggage":[

                                ],
                                "brandId":null
                            }
                        ]
                    },
                    {
                        "duration":1233,
                        "segments":[
                            {
                                "departureTime":"2021-09-10 10:27",
                                "arrivalTime":"2021-09-10 12:34",
                                "flightNumber":"3864",
                                "bookingClass":"E",
                                "stop":0,
                                "stops":[

                                ],
                                "duration":127,
                                "departureAirportCode":"SMF",
                                "departureAirportTerminal":null,
                                "arrivalAirportCode":"SEA",
                                "arrivalAirportTerminal":null,
                                "operatingAirline":null,
                                "airEquipType":"E7W",
                                "marketingAirline":"DL",
                                "marriageGroup":"",
                                "mileage":null,
                                "cabin":"Y",
                                "meal":null,
                                "fareCode":null,
                                "baggage":[

                                ],
                                "brandId":null
                            },
                            {
                                "departureTime":"2021-09-10 08:13",
                                "arrivalTime":"2021-09-10 13:34",
                                "flightNumber":"759",
                                "bookingClass":"E",
                                "stop":0,
                                "stops":[

                                ],
                                "duration":201,
                                "departureAirportCode":"SEA",
                                "departureAirportTerminal":null,
                                "arrivalAirportCode":"MSP",
                                "arrivalAirportTerminal":null,
                                "operatingAirline":null,
                                "airEquipType":"739",
                                "marketingAirline":"DL",
                                "marriageGroup":"",
                                "mileage":null,
                                "cabin":"Y",
                                "meal":null,
                                "fareCode":null,
                                "baggage":[

                                ],
                                "brandId":null
                            },
                            {
                                "departureTime":"2021-09-10 16:45",
                                "arrivalTime":"2021-09-11 08:15",
                                "flightNumber":"42",
                                "bookingClass":"E",
                                "stop":0,
                                "stops":[

                                ],
                                "duration":510,
                                "departureAirportCode":"MSP",
                                "departureAirportTerminal":null,
                                "arrivalAirportCode":"CDG",
                                "arrivalAirportTerminal":null,
                                "operatingAirline":null,
                                "airEquipType":"333",
                                "marketingAirline":"DL",
                                "marriageGroup":"",
                                "mileage":null,
                                "cabin":"Y",
                                "meal":null,
                                "fareCode":null,
                                "baggage":[

                                ],
                                "brandId":null
                            },
                            {
                                "departureTime":"2021-09-11 10:15",
                                "arrivalTime":"2021-09-11 16:50",
                                "flightNumber":"7351",
                                "bookingClass":"E",
                                "stop":1,
                                "stops":[
                                    {
                                        "locationCode":"BKO",
                                        "departureDateTime":"2021-09-11 15:20",
                                        "arrivalDateTime":"2021-09-11 13:55",
                                        "duration":85,
                                        "elapsedTime":null,
                                        "equipment":null
                                    }
                                ],
                                "duration":395,
                                "departureAirportCode":"CDG",
                                "departureAirportTerminal":null,
                                "arrivalAirportCode":"ROB",
                                "arrivalAirportTerminal":null,
                                "operatingAirline":null,
                                "airEquipType":"359",
                                "marketingAirline":"DL",
                                "marriageGroup":"",
                                "mileage":null,
                                "cabin":"Y",
                                "meal":null,
                                "fareCode":null,
                                "baggage":[

                                ],
                                "brandId":null
                            }
                        ]
                    }
                ]
            }
          }
     *  }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     * {
     *        "status": 200,
     *        "message": "OK",
     *        "data": {
     *            "resultMessage": "FlightRequest created",
     *            "id" => 12345
     *        },
     *        "technical": {
     *           ...
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     *
     * @apiErrorExample {json} Error-Response:
     * HTTP/1.1 400 Bad Request
     * {
     *        "status": 400,
     *        "message": "FlightRequest save is failed.",
     *        "technical": {
     *           ...
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     *
     * @apiErrorExample {json} Error-Response (500):
     * HTTP/1.1 500 Internal Server Error
     * {
     *        "status": "Failed",
     *        "source": {
     *            "type": 1,
     *            "status": 500
     *        },
     *        "technical": {
     *           ...
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     *
     * @apiErrorExample {json} Error-Response (422):
     * HTTP/1.1 422 Unprocessable entity
     * {
     *        "status": "Failed",
     *        "message": "Curl error: #28 - Operation timed out after 30001 milliseconds with 0 bytes received",
     *        "errors": [
     *              "Curl error: #28 - Operation timed out after 30001 milliseconds with 0 bytes received"
     *        ],
     *        "code": 0,
     *        "technical": {
     *           ...
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     */
    public function actionReprotectionCreate()
    {
        try {
            $post = Yii::$app->request->post();
        } catch (\Throwable $throwable) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::POST_DATA_ERROR),
                new ErrorsMessage($throwable->getMessage()),
            );
        }
        $reprotectionCreateForm = new ReprotectionCreateForm();

        if (!$reprotectionCreateForm->load($post)) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found data on POST request'),
            );
        }
        if (!$reprotectionCreateForm->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($reprotectionCreateForm->getErrors()),
            );
        }

        $hash = FlightRequest::generateHashFromDataJson($post);
        if (FlightRequest::find()->where(['fr_hash' => $hash])->andWhere(['fr_status_id' => FlightRequest::STATUS_NEW])->exists()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage('Flight request already exists in the "NEW" status. Hash (' . $hash . ')'),
            );
        }

        if ($reprotectionCreateForm->is_automate && empty($reprotectionCreateForm->flight_quote)) {
            $reprotectionCreateForm->is_automate = false;
            $dataMessage['warning'] = '"is_automate" parameter set to FALSE because "flight_quote" is empty';
        }

        try {
            $apiUserId = $this->auth->getId();
            $resultId = $this->transactionManager->wrap(function () use ($reprotectionCreateForm, $apiUserId, $post) {
                $flightRequest = FlightRequest::create(
                    $reprotectionCreateForm->booking_id,
                    FlightRequest::TYPE_RE_PROTECTION_CREATE,
                    $post,
                    $reprotectionCreateForm->getProject()->id,
                    $apiUserId
                );
                $flightRequest = (new FlightRequestRepository())->save($flightRequest);

                $job = new ReprotectionCreateJob();
                $job->flight_request_id = $flightRequest->fr_id;
                $job->flight_request_is_automate = $reprotectionCreateForm->is_automate;
                $jobId = Yii::$app->queue_job->priority(100)->push($job);

                $flightRequest->fr_job_id = $jobId;
                (new FlightRequestRepository())->save($flightRequest);

                return $flightRequest->fr_id;
            });
            $dataMessage['resultMessage'] = 'FlightRequest created';
            $dataMessage['id'] = $resultId;
        } catch (\Throwable $throwable) {
            \Yii::error(
                ArrayHelper::merge(AppHelper::throwableLog($throwable), $post),
                'FlightController:actionReprotectionCreate:Throwable'
            );
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('FlightRequest save is failed. ' . $throwable->getMessage()),
            );
        }

        return new SuccessResponse(
            new DataMessage($dataMessage)
        );
    }

    /**
     * @api {get} /v2/flight/reprotection-get Get flight reprotection
     * @apiVersion 0.1.0
     * @apiName ReProtection Get
     * @apiGroup Flight
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{32}}           flight_product_quote_gid                  Flight Product Quote gid
     *
     * @apiParamExample {json} Request-Example:
     *   {
     *      "flight_product_quote_gid": "2bd12377691f282e11af12937674e3d1",
     *  }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     * {
            "status": 200,
            "message": "OK",
            "origin_product_quote": {
                "pq_gid": "22c3c0c2982108117d1952f317f568a3",
                "pq_name": "",
                "pq_order_id": null,
                "pq_description": null,
                "pq_status_id": 1,
                "pq_price": 1554.4,
                "pq_origin_price": 1414.4,
                "pq_client_price": 1554.4,
                "pq_service_fee_sum": 0,
                "pq_origin_currency": "USD",
                "pq_client_currency": "USD",
                "pq_status_name": "New",
                "pq_files": [],
                "data": {
                    "fq_flight_id": 344,
                    "fq_source_id": null,
                    "fq_product_quote_id": 775,
                    "gds": "T",
                    "pcc": "E9V",
                    "fq_gds_offer_id": null,
                    "fq_type_id": 0,
                    "fq_cabin_class": "E",
                    "fq_trip_type_id": 3,
                    "validatingCarrier": "OS",
                    "fq_fare_type_id": 1,
                    "fq_origin_search_data": "{\"key\":\"2_U0FMMTAxKlkyMTAwL0tJVkxPTjIwMjItMDEtMTIvTE9ORlJBMjAyMi0wMS0xNS9GUkFLSVYyMDIyLTAxLTI0Kk9TfiNPUzY1NiNPUzQ1NSNMSDkwNSNMSDE0NzR+bGM6ZW5fdXM=\",\"routingId\":1,\"prices\":{\"lastTicketDate\":\"2021-07-31\",\"totalPrice\":1414.4,\"totalTax\":872.4,\"comm\":0,\"isCk\":false,\"markupId\":0,\"markupUid\":\"\",\"markup\":0},\"passengers\":{\"ADT\":{\"codeAs\":\"ADT\",\"cnt\":2,\"baseFare\":197,\"pubBaseFare\":197,\"baseTax\":296.8,\"markup\":0,\"comm\":0,\"price\":493.8,\"tax\":296.8,\"oBaseFare\":{\"amount\":197,\"currency\":\"USD\"},\"oBaseTax\":{\"amount\":296.8,\"currency\":\"USD\"}},\"CHD\":{\"codeAs\":\"CHD\",\"cnt\":1,\"baseFare\":148,\"pubBaseFare\":148,\"baseTax\":278.8,\"markup\":0,\"comm\":0,\"price\":426.8,\"tax\":278.8,\"oBaseFare\":{\"amount\":148,\"currency\":\"USD\"},\"oBaseTax\":{\"amount\":278.8,\"currency\":\"USD\"}}},\"penalties\":{\"exchange\":true,\"refund\":false,\"list\":[{\"type\":\"ex\",\"applicability\":\"before\",\"permitted\":true,\"amount\":0},{\"type\":\"ex\",\"applicability\":\"after\",\"permitted\":true,\"amount\":0},{\"type\":\"re\",\"applicability\":\"before\",\"permitted\":false},{\"type\":\"re\",\"applicability\":\"after\",\"permitted\":false}]},\"trips\":[{\"tripId\":1,\"segments\":[{\"segmentId\":1,\"departureTime\":\"2022-01-12 16:00\",\"arrivalTime\":\"2022-01-12 16:45\",\"stop\":0,\"stops\":[],\"flightNumber\":\"656\",\"bookingClass\":\"K\",\"duration\":105,\"departureAirportCode\":\"KIV\",\"departureAirportTerminal\":\"\",\"arrivalAirportCode\":\"VIE\",\"arrivalAirportTerminal\":\"3\",\"operatingAirline\":\"OS\",\"airEquipType\":\"E95\",\"marketingAirline\":\"OS\",\"marriageGroup\":\"I\",\"mileage\":583,\"cabin\":\"Y\",\"meal\":\"\",\"fareCode\":\"K03CLSE8\",\"baggage\":{\"ADT\":{\"carryOn\":true,\"allowPieces\":1},\"CHD\":{\"carryOn\":true,\"allowPieces\":1}},\"recheckBaggage\":false},{\"segmentId\":2,\"departureTime\":\"2022-01-12 17:15\",\"arrivalTime\":\"2022-01-12 18:40\",\"stop\":0,\"stops\":[],\"flightNumber\":\"455\",\"bookingClass\":\"K\",\"duration\":145,\"departureAirportCode\":\"VIE\",\"departureAirportTerminal\":\"3\",\"arrivalAirportCode\":\"LHR\",\"arrivalAirportTerminal\":\"2\",\"operatingAirline\":\"OS\",\"airEquipType\":\"321\",\"marketingAirline\":\"OS\",\"marriageGroup\":\"O\",\"mileage\":774,\"cabin\":\"Y\",\"meal\":\"\",\"fareCode\":\"K03CLSE8\",\"baggage\":{\"ADT\":{\"carryOn\":true,\"allowPieces\":1},\"CHD\":{\"carryOn\":true,\"allowPieces\":1}},\"recheckBaggage\":false}],\"duration\":280},{\"tripId\":2,\"segments\":[{\"segmentId\":1,\"departureTime\":\"2022-01-15 11:30\",\"arrivalTime\":\"2022-01-15 14:05\",\"stop\":0,\"stops\":[],\"flightNumber\":\"905\",\"bookingClass\":\"Q\",\"duration\":95,\"departureAirportCode\":\"LHR\",\"departureAirportTerminal\":\"2\",\"arrivalAirportCode\":\"FRA\",\"arrivalAirportTerminal\":\"1\",\"operatingAirline\":\"LH\",\"airEquipType\":\"32N\",\"marketingAirline\":\"LH\",\"marriageGroup\":\"O\",\"mileage\":390,\"cabin\":\"Y\",\"meal\":\"\",\"fareCode\":\"Q03CLSE0\",\"baggage\":{\"ADT\":{\"carryOn\":true,\"allowPieces\":1},\"CHD\":{\"carryOn\":true,\"allowPieces\":1}},\"recheckBaggage\":false}],\"duration\":95},{\"tripId\":3,\"segments\":[{\"segmentId\":1,\"departureTime\":\"2022-01-24 09:45\",\"arrivalTime\":\"2022-01-24 13:05\",\"stop\":0,\"stops\":[],\"flightNumber\":\"1474\",\"bookingClass\":\"Q\",\"duration\":140,\"departureAirportCode\":\"FRA\",\"departureAirportTerminal\":\"1\",\"arrivalAirportCode\":\"KIV\",\"arrivalAirportTerminal\":\"\",\"operatingAirline\":\"CL\",\"opName\":\"LUFTHANSA CITYLINE GMBH\",\"airEquipType\":\"E90\",\"marketingAirline\":\"LH\",\"marriageGroup\":\"O\",\"mileage\":953,\"cabin\":\"Y\",\"meal\":\"\",\"fareCode\":\"Q03CLSE0\",\"baggage\":{\"ADT\":{\"carryOn\":true,\"allowPieces\":1},\"CHD\":{\"carryOn\":true,\"allowPieces\":1}},\"recheckBaggage\":false}],\"duration\":140}],\"maxSeats\":9,\"paxCnt\":3,\"validatingCarrier\":\"OS\",\"gds\":\"T\",\"pcc\":\"E9V\",\"cons\":\"GTT\",\"fareType\":\"PUB\",\"tripType\":\"MC\",\"cabin\":\"Y\",\"currency\":\"USD\",\"currencies\":[\"USD\"],\"currencyRates\":{\"USDUSD\":{\"from\":\"USD\",\"to\":\"USD\",\"rate\":1}},\"keys\":{\"travelport\":{\"traceId\":\"23d8e32d-b8eb-4578-9928-4674761747d6\",\"availabilitySources\":\"Q,Q,S,S\",\"type\":\"T\"},\"seatHoldSeg\":{\"trip\":0,\"segment\":0,\"seats\":9}},\"ngsFeatures\":{\"stars\":1,\"name\":\"BASIC\",\"list\":[]},\"meta\":{\"eip\":0,\"noavail\":false,\"searchId\":\"U0FMMTAxWTIxMDB8S0lWTE9OMjAyMi0wMS0xMnxMT05GUkEyMDIyLTAxLTE1fEZSQUtJVjIwMjItMDEtMjQ=\",\"lang\":\"en\",\"rank\":10,\"cheapest\":true,\"fastest\":true,\"best\":true,\"bags\":1,\"country\":\"us\",\"prod_types\":[\"PUB\"]},\"price\":493.8,\"originRate\":1,\"stops\":[1,0,0],\"time\":[{\"departure\":\"2022-01-12 16:00\",\"arrival\":\"2022-01-12 18:40\"},{\"departure\":\"2022-01-15 11:30\",\"arrival\":\"2022-01-15 14:05\"},{\"departure\":\"2022-01-24 09:45\",\"arrival\":\"2022-01-24 13:05\"}],\"bagFilter\":1,\"airportChange\":false,\"technicalStopCnt\":0,\"duration\":[280,95,140],\"totalDuration\":515,\"topCriteria\":\"fastestbestcheapest\",\"rank\":10}",
                    "fq_last_ticket_date": "2021-07-31",
                    "fq_json_booking": null,
                    "fq_ticket_json": null,
                    "itineraryDump": [
                        "1  AF6602E  25MAR  KIVOTP    525A    640A  TH OPERATED BY RO",
                        "2  AF1889E  25MAR  OTPCDG    225P    435P  TH",
                        "3  AF1380E  25MAR  CDGLHR    920P    945P  TH"
                    ],
                    "booking_id": "O230850",
                    "fq_type_name": "Base",
                    "fareType": "PUB",
                    "flight": {
                        "fl_product_id": 688,
                        "fl_trip_type_id": 3,
                        "fl_cabin_class": "E",
                        "fl_adults": 2,
                        "fl_children": 1,
                        "fl_infants": 0,
                        "fl_trip_type_name": "Multi destination",
                        "fl_cabin_class_name": "Economy"
                    },
                    "trips": [
                        {
                            "uid": "fqt6103c94699a2e",
                            "key": null,
                            "duration": 280,
                            "segments": [
                                {
                                    "uid": "fqs6103c9469c3c8",
                                    "departureTime": "2022-01-12 16:00",
                                    "arrivalTime": "2022-01-12 16:45",
                                    "flightNumber": 656,
                                    "bookingClass": "K",
                                    "duration": 105,
                                    "departureAirportCode": "KIV",
                                    "departureAirportTerminal": "",
                                    "arrivalAirportCode": "VIE",
                                    "arrivalAirportTerminal": "3",
                                    "fqs_operating_airline": "RO",
                                    "fqs_marketing_airline": "RO",
                                    "airEquipType": "E95",
                                    "marriageGroup": "I",
                                    "meal": "",
                                    "fareCode": "K03CLSE8",
                                    "mileage": 583,
                                    "departureLocation": "Chisinau",
                                    "arrivalLocation": "Vienna",
                                    "cabin": "E",
                                    "operatingAirline": "RO",
                                    "marketingAirline": "RO",
                                    "stop": 1,
                                    "stops": [
                                        {
                                            "qss_quote_segment_id": 9,
                                            "locationCode": "SCL",
                                            "equipment": "",
                                            "elapsedTime": 120,
                                            "duration": 120,
                                            "departureDateTime": "2021-09-09 00:00",
                                            "arrivalDateTime": "2021-09-08 00:00"
                                        }
                                    ],
                                    "baggage": [
                                        {
                                            "qsb_flight_pax_code_id": 1,
                                            "qsb_flight_quote_segment_id": 1076,
                                            "qsb_airline_code": null,
                                            "qsb_allow_pieces": 1,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null,
                                            "qsb_carry_one": 1
                                        },
                                        {
                                            "qsb_flight_pax_code_id": 2,
                                            "qsb_flight_quote_segment_id": 1076,
                                            "qsb_airline_code": null,
                                            "qsb_allow_pieces": 1,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null,
                                            "qsb_carry_one": 1
                                        }
                                    ]
                                },
                                {
                                    "uid": "fqs6103c9469e37b",
                                    "departureTime": "2022-01-12 17:15",
                                    "arrivalTime": "2022-01-12 18:40",
                                    "flightNumber": 455,
                                    "bookingClass": "K",
                                    "duration": 145,
                                    "departureAirportCode": "VIE",
                                    "departureAirportTerminal": "3",
                                    "arrivalAirportCode": "LHR",
                                    "arrivalAirportTerminal": "2",
                                    "fqs_operating_airline": "OS",
                                    "fqs_marketing_airline": "OS",
                                    "airEquipType": "321",
                                    "marriageGroup": "O",
                                    "meal": "",
                                    "fareCode": "K03CLSE8",
                                    "mileage": 774,
                                    "departureLocation": "Vienna",
                                    "arrivalLocation": "London",
                                    "cabin": "E",
                                    "operatingAirline": "OS",
                                    "marketingAirline": "OS",
                                    "stop": 0,
                                    "stops": [],
                                    "baggage": [
                                        {
                                            "qsb_flight_pax_code_id": 1,
                                            "qsb_flight_quote_segment_id": 1077,
                                            "qsb_airline_code": null,
                                            "qsb_allow_pieces": 1,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null,
                                            "qsb_carry_one": 1
                                        },
                                        {
                                            "qsb_flight_pax_code_id": 2,
                                            "qsb_flight_quote_segment_id": 1077,
                                            "qsb_airline_code": null,
                                            "qsb_allow_pieces": 1,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null,
                                            "qsb_carry_one": 1
                                        }
                                    ]
                                }
                            ]
                        },
                        {
                            "uid": "fqt6103c9469f378",
                            "key": null,
                            "duration": 95,
                            "segments": [
                                {
                                    "uid": "fqs6103c9469fa85",
                                    "departureTime": "2022-01-15 11:30",
                                    "arrivalTime": "2022-01-15 14:05",
                                    "flightNumber": 905,
                                    "bookingClass": "Q",
                                    "duration": 95,
                                    "departureAirportCode": "LHR",
                                    "departureAirportTerminal": "2",
                                    "arrivalAirportCode": "FRA",
                                    "arrivalAirportTerminal": "1",
                                    "fqs_operating_airline": "LH",
                                    "fqs_marketing_airline": "LH",
                                    "airEquipType": "32N",
                                    "marriageGroup": "O",
                                    "cabin": "Y",
                                    "meal": "",
                                    "fareCode": "Q03CLSE0",
                                    "mileage": 390,
                                    "departureLocation": "London",
                                    "arrivalLocation": "Frankfurt am Main",
                                    "operatingAirline": "LH",
                                    "marketingAirline": "LH",
                                    "stop": 0,
                                    "stops": [],
                                    "baggage": [
                                        {
                                            "qsb_flight_pax_code_id": 1,
                                            "qsb_flight_quote_segment_id": 1078,
                                            "qsb_airline_code": null,
                                            "qsb_allow_pieces": 1,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null,
                                            "qsb_carry_one": 1
                                        },
                                        {
                                            "qsb_flight_pax_code_id": 2,
                                            "qsb_flight_quote_segment_id": 1078,
                                            "qsb_airline_code": null,
                                            "qsb_allow_pieces": 1,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null,
                                            "qsb_carry_one": 1
                                        }
                                    ]
                                }
                            ]
                        },
                        {
                            "uid": "fqt6103c946a08d6",
                            "key": null,
                            "duration": 140,
                            "segments": [
                                {
                                    "uid": "fqs6103c946a0d33",
                                    "departureTime": "2022-01-24 09:45",
                                    "arrivalTime": "2022-01-24 13:05",
                                    "flightNumber": 1474,
                                    "bookingClass": "Q",
                                    "duration": 140,
                                    "departureAirportCode": "FRA",
                                    "departureAirportTerminal": "1",
                                    "arrivalAirportCode": "KIV",
                                    "arrivalAirportTerminal": "",
                                    "fqs_operating_airline": "RO",
                                    "fqs_marketing_airline": "RO",
                                    "airEquipType": "E90",
                                    "marriageGroup": "O",
                                    "meal": "",
                                    "fareCode": "Q03CLSE0",
                                    "mileage": 953,
                                    "departureLocation": "Frankfurt am Main",
                                    "arrivalLocation": "Chisinau",
                                    "cabin": "E",
                                    "operatingAirline": "LH",
                                    "marketingAirline": "LH",
                                    "stop": 0,
                                    "stops": [],
                                    "baggage": [
                                        {
                                            "qsb_flight_pax_code_id": 1,
                                            "qsb_flight_quote_segment_id": 1079,
                                            "qsb_airline_code": null,
                                            "qsb_allow_pieces": 1,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null,
                                            "qsb_carry_one": 1
                                        },
                                        {
                                            "qsb_flight_pax_code_id": 2,
                                            "qsb_flight_quote_segment_id": 1079,
                                            "qsb_airline_code": null,
                                            "qsb_allow_pieces": 1,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null,
                                            "qsb_carry_one": 1
                                        }
                                    ]
                                }
                            ]
                        }
                    ],
                    "pax_prices": [
                        {
                            "qpp_fare": "197.00",
                            "qpp_tax": "296.80",
                            "qpp_system_mark_up": "0.00",
                            "qpp_agent_mark_up": "70.00",
                            "qpp_origin_fare": "197.00",
                            "qpp_origin_currency": "USD",
                            "qpp_origin_tax": "296.80",
                            "qpp_client_currency": "USD",
                            "qpp_client_fare": "197.00",
                            "qpp_client_tax": "296.80",
                            "paxType": "ADT"
                        },
                        {
                            "qpp_fare": "148.00",
                            "qpp_tax": "278.80",
                            "qpp_system_mark_up": "0.00",
                            "qpp_agent_mark_up": "0.00",
                            "qpp_origin_fare": "148.00",
                            "qpp_origin_currency": "USD",
                            "qpp_origin_tax": "278.80",
                            "qpp_client_currency": "USD",
                            "qpp_client_fare": "148.00",
                            "qpp_client_tax": "278.80",
                            "paxType": "CHD"
                        }
                    ],
                    "paxes": [
                        {
                            "fp_uid": "fp6103c94694091",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        },
                        {
                            "fp_uid": "fp6103c946948e9",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        },
                        {
                            "fp_uid": "fp6103c94695639",
                            "fp_pax_id": null,
                            "fp_pax_type": "CHD",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        }
                    ]
                }
            },
            "reprotection_product_quote": {
                "pq_gid": "2bd12377691f282e11af12937674e3d1",
                "pq_name": "",
                "pq_order_id": 544,
                "pq_description": null,
                "pq_status_id": 1,
                "pq_price": 274.7,
                "pq_origin_price": 259.86,
                "pq_client_price": 274.7,
                "pq_service_fee_sum": 0,
                "pq_origin_currency": "USD",
                "pq_client_currency": "USD",
                "pq_status_name": "New",
                "pq_files": [],
                "data": {
                    "fq_flight_id": 343,
                    "fq_source_id": null,
                    "fq_product_quote_id": 774,
                    "gds": "C",
                    "pcc": "default",
                    "fq_gds_offer_id": null,
                    "fq_type_id": 0,
                    "fq_cabin_class": "E",
                    "fq_trip_type_id": 1,
                    "validatingCarrier": "RO",
                    "fq_fare_type_id": 1,
                    "fq_origin_search_data": "{\"key\":\"2_U0FMMTAxKlkyMDAwL0tJVkxPTjIwMjEtMDctMjkqUk9+I1JPMjAyI1JPMzkxfmxjOmVuX3Vz\",\"routingId\":2,\"prices\":{\"lastTicketDate\":\"2021-07-28 23:59\",\"totalPrice\":302.9,\"totalTax\":81.5,\"comm\":0,\"isCk\":true,\"CkAmount\":14.84,\"markupId\":0,\"markupUid\":\"\",\"markup\":14.84},\"passengers\":{\"ADT\":{\"codeAs\":\"ADT\",\"cnt\":2,\"baseFare\":110.7,\"pubBaseFare\":110.7,\"baseTax\":33.33,\"markup\":7.42,\"comm\":0,\"CkAmount\":7.42,\"price\":151.45,\"tax\":40.75,\"oBaseFare\":{\"amount\":92,\"currency\":\"EUR\"},\"oBaseTax\":{\"amount\":27.7,\"currency\":\"EUR\"},\"oCkAmount\":{\"amount\":6.17,\"currency\":\"EUR\"}}},\"trips\":[{\"tripId\":1,\"segments\":[{\"segmentId\":1,\"departureTime\":\"2021-07-29 09:30\",\"arrivalTime\":\"2021-07-29 10:45\",\"stop\":0,\"stops\":null,\"flightNumber\":\"202\",\"bookingClass\":\"E\",\"duration\":75,\"departureAirportCode\":\"KIV\",\"departureAirportTerminal\":\"\",\"arrivalAirportCode\":\"OTP\",\"arrivalAirportTerminal\":\"\",\"operatingAirline\":\"RO\",\"airEquipType\":\"AT7\",\"marketingAirline\":\"RO\",\"marriageGroup\":\"\",\"cabin\":\"Y\",\"meal\":\"\",\"fareCode\":\"EOWSVRMD\",\"baggage\":{\"ADT\":{\"carryOn\":true,\"allowPieces\":1}},\"recheckBaggage\":false},{\"segmentId\":2,\"departureTime\":\"2021-07-29 12:20\",\"arrivalTime\":\"2021-07-29 14:05\",\"stop\":0,\"stops\":null,\"flightNumber\":\"391\",\"bookingClass\":\"E\",\"duration\":225,\"departureAirportCode\":\"OTP\",\"departureAirportTerminal\":\"\",\"arrivalAirportCode\":\"LHR\",\"arrivalAirportTerminal\":\"\",\"operatingAirline\":\"RO\",\"airEquipType\":\"318\",\"marketingAirline\":\"RO\",\"marriageGroup\":\"\",\"cabin\":\"Y\",\"meal\":\"\",\"fareCode\":\"EOWSVRGB\",\"baggage\":{\"ADT\":{\"carryOn\":true,\"allowPieces\":1}},\"recheckBaggage\":false}],\"duration\":395}],\"maxSeats\":3,\"paxCnt\":2,\"validatingCarrier\":\"RO\",\"gds\":\"C\",\"pcc\":\"default\",\"cons\":\"AER\",\"fareType\":\"PUB\",\"tripType\":\"OW\",\"cabin\":\"Y\",\"currency\":\"USD\",\"currencies\":[\"USD\",\"EUR\"],\"currencyRates\":{\"EURUSD\":{\"from\":\"EUR\",\"to\":\"USD\",\"rate\":1.20328},\"USDUSD\":{\"from\":\"USD\",\"to\":\"USD\",\"rate\":1}},\"keys\":{\"cockpit\":{\"itineraryIds\":[\"D3537439481d_ROUNDTRIP_0_0_0_0\"],\"fareIds\":[\"D3537439481d_ROUNDTRIP_0\"],\"webServiceLogId\":\"EM483101d9441a09d\",\"sessionId\":\"3af91858-e306-4b40-83af-108c593f2a36\",\"type\":\"C\"}},\"ngsFeatures\":{\"stars\":1,\"name\":\"BASIC\",\"list\":[]},\"meta\":{\"eip\":0,\"noavail\":false,\"searchId\":\"U0FMMTAxWTIwMDB8S0lWTE9OMjAyMS0wNy0yOQ==\",\"lang\":\"en\",\"rank\":8.987654,\"cheapest\":false,\"fastest\":false,\"best\":false,\"bags\":1,\"country\":\"us\",\"prod_types\":[\"PUB\"]},\"price\":151.45,\"originRate\":1,\"stops\":[1],\"time\":[{\"departure\":\"2021-07-29 09:30\",\"arrival\":\"2021-07-29 14:05\"}],\"bagFilter\":1,\"airportChange\":false,\"technicalStopCnt\":0,\"duration\":[395],\"totalDuration\":395,\"topCriteria\":\"\",\"rank\":8.987654}",
                    "fq_last_ticket_date": "2021-07-28",
                    "fq_json_booking": null,
                    "fq_ticket_json": null,
                    "itineraryDump": [
                        "1  AF6602E  25MAR  KIVOTP    525A    640A  TH OPERATED BY RO",
                        "2  AF1889E  25MAR  OTPCDG    225P    435P  TH",
                        "3  AF1380E  25MAR  CDGLHR    920P    945P  TH"
                    ],
                    "booking_id": "O230851",
                    "fq_type_name": "Base",
                    "fareType": "PUB",
                    "flight": {
                        "fl_product_id": 687,
                        "fl_trip_type_id": 1,
                        "fl_cabin_class": "E",
                        "fl_adults": 2,
                        "fl_children": 0,
                        "fl_infants": 0,
                        "fl_trip_type_name": "One Way",
                        "fl_cabin_class_name": "Economy"
                    },
                    "trips": [
                        {
                            "uid": "fqt61015f35534ec",
                            "key": null,
                            "duration": 395,
                            "segments": [
                                {
                                    "uid": "fqs61015f3554892",
                                    "departureTime": "2021-07-29 09:30",
                                    "arrivalTime": "2021-07-29 10:45",
                                    "flightNumber": 202,
                                    "bookingClass": "E",
                                    "duration": 75,
                                    "departureAirportCode": "KIV",
                                    "departureAirportTerminal": "",
                                    "arrivalAirportCode": "OTP",
                                    "arrivalAirportTerminal": "",
                                    "fqs_operating_airline": "RO",
                                    "fqs_marketing_airline": "RO",
                                    "airEquipType": "AT7",
                                    "marriageGroup": "",
                                    "meal": "",
                                    "fareCode": "EOWSVRMD",
                                    "mileage": null,
                                    "departureLocation": "Chisinau",
                                    "arrivalLocation": "Bucharest",
                                    "cabin": "E",
                                    "operatingAirline": "RO",
                                    "marketingAirline": "RO",
                                    "stop": 0,
                                    "stops": [],
                                    "baggage": [
                                        {
                                            "qsb_flight_pax_code_id": 1,
                                            "qsb_flight_quote_segment_id": 1074,
                                            "qsb_airline_code": null,
                                            "qsb_allow_pieces": 1,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null,
                                            "qsb_carry_one": 1
                                        }
                                    ]
                                },
                                {
                                    "uid": "fqs61015f35565ef",
                                    "departureTime": "2021-07-29 12:20",
                                    "arrivalTime": "2021-07-29 14:05",
                                    "flightNumber": 391,
                                    "bookingClass": "E",
                                    "duration": 225,
                                    "departureAirportCode": "OTP",
                                    "departureAirportTerminal": "",
                                    "arrivalAirportCode": "LHR",
                                    "arrivalAirportTerminal": "",
                                    "fqs_operating_airline": "RO",
                                    "fqs_marketing_airline": "RO",
                                    "airEquipType": "318",
                                    "marriageGroup": "",
                                    "meal": "",
                                    "fareCode": "EOWSVRGB",
                                    "mileage": null,
                                    "departureLocation": "Bucharest",
                                    "arrivalLocation": "London",
                                    "cabin": "E",
                                    "operatingAirline": "RO",
                                    "marketingAirline": "RO",
                                    "stop": 0,
                                    "stops": [],
                                    "baggage": [
                                        {
                                            "qsb_flight_pax_code_id": 1,
                                            "qsb_flight_quote_segment_id": 1075,
                                            "qsb_airline_code": null,
                                            "qsb_allow_pieces": 1,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null,
                                            "qsb_carry_one": 1
                                        }
                                    ]
                                }
                            ]
                        }
                    ],
                    "pax_prices": [
                        {
                            "qpp_fare": "99.86",
                            "qpp_tax": "30.07",
                            "qpp_system_mark_up": "7.42",
                            "qpp_agent_mark_up": "0.00",
                            "qpp_origin_fare": "110.70",
                            "qpp_origin_currency": "USD",
                            "qpp_origin_tax": "33.33",
                            "qpp_client_currency": "USD",
                            "qpp_client_fare": "99.86",
                            "qpp_client_tax": "30.07",
                            "paxType": "ADT"
                        }
                    ],
                    "paxes": [
                        {
                            "fp_uid": "fp61015f33cccbd",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        },
                        {
                            "fp_uid": "fp61015f33cd1f4",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        },
                        {
                            "fp_uid": "fp61015f354f612",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        },
                        {
                            "fp_uid": "fp61015f354f948",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        }
                    ]
                }
            },
            "order": {
                "or_id": 544,
                "or_gid": "3b78e38c2ae14e4ad282cf3abc652140",
                "or_uid": "or61015f39e2d71",
                "or_name": "Order 1",
                "or_description": null,
                "or_status_id": 2,
                "or_pay_status_id": 1,
                "or_app_total": "274.70",
                "or_app_markup": "14.84",
                "or_agent_markup": "0.00",
                "or_client_total": "274.70",
                "or_client_currency": "USD",
                "or_client_currency_rate": "1.00000",
                "or_status_name": "Pending",
                "or_pay_status_name": "Not paid",
                "or_client_currency_symbol": "USD",
                "or_files": [],
                "or_request_uid": null,
                "billing_info": []
            },
            "order_contacts": []
        }
     *
     * @apiErrorExample {json} Error-Response:
     * HTTP/1.1 200 Ok
     * {
            "status": 422,
            "message": "Product Quote not found",
            "errors": [
                "Product Quote not found"
            ],
            "code": 0
        }
     *
     * @apiErrorExample {json} Error-Response (500):
     * HTTP/1.1 200 Ok
     * {
            "status": 500,
            "message": "Internal Server Error",
            "code": 8,
            "errors": []
        }
     *
     */
    public function actionReprotectionGet()
    {
        $form = new ReprotectionGetForm();

        if (!$form->load(Yii::$app->request->get())) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found data on GET request'),
            );
        }
        if (!$form->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($form->getErrors()),
            );
        }

        try {
            $productQuote = $this->productQuoteRepository->findByGidFlightProductQuote($form->flight_product_quote_gid);

            $originProductQuote = $productQuote->relateParent ? $productQuote->relateParent->toArray() : [];

            $order = $productQuote->pqOrder;

            $orderSerialized = $order ? $order->serialize() : [];
            if ($orderSerialized && array_key_exists('quotes', $orderSerialized)) {
                unset($orderSerialized['quotes']);
            }

            $orderContacts = [];
            foreach ($order->orderContacts ?? [] as $orderContact) {
                $orderContacts[] = $orderContact->serialize();
            }

            return new SuccessResponse(
                new Message('origin_product_quote', $originProductQuote),
                new Message('reprotection_product_quote', $productQuote->toArray()),
                new Message('order', $orderSerialized),
                new Message('order_contacts', $orderContacts)
            );
        } catch (NotFoundException | \RuntimeException $e) {
            return new ErrorResponse(
                new StatusCodeMessage(422),
                new MessageMessage($e->getMessage()),
                new ErrorsMessage($e->getMessage()),
                new CodeMessage($e->getCode())
            );
        } catch (\Throwable $e) {
            \Yii::error(AppHelper::throwableLog($e), 'API:FlightController:actionReprotectionGet:Throwable');
            return new ErrorResponse(
                new StatusCodeMessage(500),
                new MessageMessage('Internal Server Error'),
                new CodeMessage($e->getCode())
            );
        }
    }

    /**
     * @api {post} /v2/flight/reprotection-decision Reprotection decision
     * @apiVersion 0.2.0
     * @apiName ReProtection Decision
     * @apiGroup Flight
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{7..10}}       booking_id Booking ID
     * @apiParam {string="confirm", "modify", "refund"}  type  Re-protection Type
     * @apiParam {string{32}}       [reprotection_quote_gid] Re-protection Product Quote GID
     * @apiParam {string}        [flight_product_quote]   Flight Quote Data (required for type = "modify")
     *
     * @apiParamExample {json} Request-Example:
     *   {
     *      "booking_id": "W12RT56",
     *      "type": "confirm",
     *      "reprotection_quote_gid": "94f95e797313c99d85d955373e408788",
     *      "flight_product_quote": "{}" // todo
     *  }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     * {
     *        "status": 200,
     *        "message": "OK",
     *        "data": {
     *            "success" => true
     *        },
     *        "technical": {
     *           ...
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     *
     * @apiErrorExample {json} Error-Response:
     * HTTP/1.1 400 Bad Request
     * {
     *        "status": 400,
     *        "message": "Load data error",
     *        "errors": [
     *           "Not found data on POST request"
     *        ],
     *        "technical": {
     *           ...
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     *
     * @apiErrorExample {json} Error-Response (Bad Request):
     * HTTP/1.1 400 Bad Request
     * {
     *        "status": 400,
     *        "message": "Error",
     *        "errors": [
     *           "Not found Project with current user: xxx"
     *        ],
     *        "code": "13101",
     *        "technical": {
     *           ...
     *        }
     * }
     *
     * * @apiErrorExample {json} Error-Response:
     * HTTP/1.1 410 Gone
     * {
     *        "status": 410,
     *        "message": "Date 2022-05-20 23:59:59 has past",
     *        "errors": [],
     *        "code": "13115",
     *        "technical": {
     *           ...
     *        }
     * }
     *
     * @apiErrorExample {json} Error-Response:
     * HTTP/1.1 422 Unprocessable entity
     * {
     *        "status": 422,
     *        "message": "Validation error",
     *        "errors": [
     *            "type": [
     *               "Type cannot be blank."
     *             ]
     *        ],
     *        "technical": {
     *           ...
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     *
     * @apiErrorExample {json} Error-Response (422) Code 101:
     * HTTP/1.1 422 Error
     * {
     *        "status": 422,
     *        "message": "Error",
     *        "data": [
     *              "success": false,
     *              "error": "Product Quote Change status is not in \"pending\". Current status Canceled"
     *        ],
     *        "code": 101,
     *        "errors": [],
     *        "technical": {
     *           ...
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     *
     * @apiErrorExample {html} Codes designation
     * [
     *      13101 - Api User has no related project
     *      13115 - Date is expired
     * ]
     */
    public function actionReprotectionDecision()
    {
        try {
            $post = Yii::$app->request->post();
        } catch (\Throwable $throwable) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::POST_DATA_ERROR),
                new ErrorsMessage($throwable->getMessage()),
            );
        }

        if (!$project = $this->auth->auProject) {
            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::BAD_REQUEST),
                new ErrorsMessage('Not found Project with current user: ' . $this->auth->au_api_username),
                new CodeMessage(ApiCodeException::NOT_FOUND_PROJECT_CURRENT_USER)
            );
        }

        $form = new reprotectionDecision\DecisionForm();
        if (!$form->load($post)) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found data on POST request'),
            );
        }

        if (!$form->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($form->getErrors()),
            );
        }

        try {
            $productQuote = ProductQuote::findByGid($form->reprotection_quote_gid);
            if ($productQuote && !ProductQuoteHelper::checkingExpirationDate($productQuote)) {
                $flightRequest = FlightRequest::create(
                    $form->booking_id,
                    FlightRequest::TYPE_VOLUNTARY_EXCHANGE_CONFIRM,
                    $form->toArray(),
                    $project->id,
                    $this->auth->getId()
                );
                $flightRequest = $this->objectCollection->getFlightRequestRepository()->save($flightRequest);

                $flightRequest->fr_job_id = \Yii::$app->queue_job
                    ->priority(10)
                    ->push(new ExchangeExpiredJob(
                        $flightRequest->fr_id,
                        $productQuote->pq_id,
                        ProductQuoteRelation::TYPE_REPROTECTION
                    ));

                return new ErrorResponse(
                    new StatusCodeMessage(HttpStatusCodeHelper::GONE),
                    new MessageMessage(sprintf('Date %s has past', $productQuote->pq_expiration_dt)),
                    new CodeMessage(ApiCodeException::DATA_EXPIRED)
                );
            }

            if ($form->isConfirm()) {
                Yii::createObject(reprotectionDecision\confirm\Confirm::class)->handle($form->reprotection_quote_gid, null);
            } elseif ($form->isModify()) {
                Yii::createObject(reprotectionDecision\modify\Modify::class)->handle($form->booking_id, $form->flight_product_quote, null);
            } elseif ($form->isRefund()) {
                Yii::createObject(reprotectionDecision\refund\Refund::class)->handle($form->booking_id, null);
            } else {
                throw new \DomainException('Undefined type');
            }

            return new SuccessResponse(
                new DataMessage([
                    'success' => true,
                ])
            );
        } catch (\DomainException $e) {
            \Yii::warning([
                'message' => 'Reprotection decision failed (DomainException). Reason: ' . $e->getMessage(),
                'error' => $e->getMessage(),
                'request' => $form->getAttributes(),
            ], 'FlightController:reprotectionDecision:DomainException');

            return new ErrorResponse(
                new DataMessage([
                    'success' => false,
                    'error' => $e->getMessage()
                ]),
                new CodeMessage($e->getCode())
            );
        } catch (\Throwable $e) {
            \Yii::warning([
                'message' => 'Reprotection decision failed (Throwable). Reason: ' . $e->getMessage(),
                'request' => $form->getAttributes(),
                'error' => $e->getMessage(),
                'exception' => AppHelper::throwableLog($e, false),
            ], 'FlightController:reprotectionDecision:Throwable');

            return new ErrorResponse(
                new DataMessage([
                    'success' => false,
                    'error' => $e->getMessage(),
                ])
            );
        }
    }

    /**
     * @api {get} /v2/flight/product-quote-get Get product quote
     * @apiVersion 0.1.0
     * @apiName ProductQuoteGet
     * @apiGroup Flight
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{32}}   product_quote_gid       Product Quote gid
     * @apiParam {string[]}     [with]                  Array ("quote_list", "last_change")
     * @apiParam {int[]=4, 5}   [onlyRelationTypes]     Only available with param "with" (4 - reprotection, 5 - voluntary exchange)
     *
     * @apiParamExample {json} Request-Example:
     *   {
     *      "product_quote_gid": "2bd12377691f282e11af12937674e3d1",
     *      "with": ["quote_list", "last_change"],
     *      "onlyRelationTypes": [4, 5],
     *  }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
        {
            "status": 200,
            "message": "OK",
            "product_quote": {
                "pq_gid": "1865ef55f3c6c01dca1f4f3128e82733",
                "pq_name": "test",
                "pq_order_id": 35,
                "pq_description": null,
                "pq_status_id": 10,
                "pq_price": 430.46,
                "pq_origin_price": 326.9,
                "pq_client_price": 430.46,
                "pq_service_fee_sum": 14.56,
                "pq_origin_currency": "USD",
                "pq_client_currency": "USD",
                "pq_status_name": "Declined",
                "pq_files": [],
                "data": {
                    "fq_flight_id": 2,
                    "fq_source_id": null,
                    "fq_product_quote_id": 184,
                    "gds": "T",
                    "pcc": "E9V",
                    "fq_gds_offer_id": null,
                    "fq_type_id": 0,
                    "fq_cabin_class": "E",
                    "fq_trip_type_id": 1,
                    "validatingCarrier": "AF",
                    "fq_fare_type_id": 1,
                    "fq_last_ticket_date": "2021-03-25",
                    "fq_origin_search_data": "{\"key\":\"2_U0FMMTAxKlkxMDAwL0tJVkxPTjIwMjEtMDMtMjUqQUZ+I0FGNjYwMiNBRjE4ODkjQUYxMzgwfmxjOmVuX3Vz\",\"routingId\":2,\"prices\":{\"lastTicketDate\":\"2021-03-25\",\"totalPrice\":326.9,\"totalTax\":55.9,\"comm\":0,\"isCk\":false,\"markupId\":0,\"markupUid\":\"\",\"markup\":0},\"passengers\":{\"ADT\":{\"codeAs\":\"ADT\",\"cnt\":1,\"baseFare\":271,\"pubBaseFare\":271,\"baseTax\":55.9,\"markup\":0,\"comm\":0,\"price\":326.9,\"tax\":55.9,\"oBaseFare\":{\"amount\":271,\"currency\":\"USD\"},\"oBaseTax\":{\"amount\":55.9,\"currency\":\"USD\"}}},\"penalties\":{\"exchange\":false,\"refund\":false,\"list\":[{\"type\":\"re\",\"applicability\":\"before\",\"permitted\":false},{\"type\":\"re\",\"applicability\":\"after\",\"permitted\":false}]},\"trips\":[{\"tripId\":1,\"segments\":[{\"segmentId\":1,\"departureTime\":\"2021-03-25 05:25\",\"arrivalTime\":\"2021-03-25 06:40\",\"stop\":0,\"stops\":[],\"flightNumber\":\"6602\",\"bookingClass\":\"E\",\"duration\":75,\"departureAirportCode\":\"KIV\",\"departureAirportTerminal\":\"\",\"arrivalAirportCode\":\"OTP\",\"arrivalAirportTerminal\":\"\",\"operatingAirline\":\"RO\",\"airEquipType\":\"AT7\",\"marketingAirline\":\"AF\",\"marriageGroup\":\"I\",\"mileage\":215,\"cabin\":\"Y\",\"brandId\":\"657936\",\"brandName\":\"Economy Standard\",\"meal\":\"\",\"fareCode\":\"ES50BBST\",\"baggage\":{\"ADT\":{\"carryOn\":true,\"allowPieces\":1}},\"recheckBaggage\":false},{\"segmentId\":2,\"departureTime\":\"2021-03-25 14:25\",\"arrivalTime\":\"2021-03-25 16:35\",\"stop\":0,\"stops\":[],\"flightNumber\":\"1889\",\"bookingClass\":\"E\",\"duration\":190,\"departureAirportCode\":\"OTP\",\"departureAirportTerminal\":\"\",\"arrivalAirportCode\":\"CDG\",\"arrivalAirportTerminal\":\"2E\",\"operatingAirline\":\"AF\",\"airEquipType\":\"319\",\"marketingAirline\":\"AF\",\"marriageGroup\":\"I\",\"mileage\":1147,\"cabin\":\"Y\",\"brandId\":\"657936\",\"brandName\":\"Economy Standard\",\"meal\":\"\",\"fareCode\":\"ES50BBST\",\"baggage\":{\"ADT\":{\"carryOn\":true,\"allowPieces\":1}},\"recheckBaggage\":false},{\"segmentId\":3,\"departureTime\":\"2021-03-25 21:20\",\"arrivalTime\":\"2021-03-25 21:45\",\"stop\":0,\"stops\":[],\"flightNumber\":\"1380\",\"bookingClass\":\"E\",\"duration\":85,\"departureAirportCode\":\"CDG\",\"departureAirportTerminal\":\"2E\",\"arrivalAirportCode\":\"LHR\",\"arrivalAirportTerminal\":\"2\",\"operatingAirline\":\"AF\",\"airEquipType\":\"318\",\"marketingAirline\":\"AF\",\"marriageGroup\":\"O\",\"mileage\":214,\"cabin\":\"Y\",\"brandId\":\"657936\",\"brandName\":\"Economy Standard\",\"meal\":\"\",\"fareCode\":\"ES50BBST\",\"baggage\":{\"ADT\":{\"carryOn\":true,\"allowPieces\":1}},\"recheckBaggage\":false}],\"duration\":1100}],\"maxSeats\":9,\"paxCnt\":1,\"validatingCarrier\":\"AF\",\"gds\":\"T\",\"pcc\":\"E9V\",\"cons\":\"GTT\",\"fareType\":\"PUB\",\"tripType\":\"OW\",\"cabin\":\"Y\",\"currency\":\"USD\",\"currencies\":[\"USD\"],\"currencyRates\":{\"USDUSD\":{\"from\":\"USD\",\"to\":\"USD\",\"rate\":1}},\"keys\":{\"travelport\":{\"traceId\":\"661f0376-d209-4216-a0d1-97c8f7cf5746\",\"availabilitySources\":\"S,S,S\",\"type\":\"T\"},\"seatHoldSeg\":{\"trip\":0,\"segment\":0,\"seats\":9}},\"ngsFeatures\":{\"stars\":1,\"name\":\"Economy Standard\",\"list\":[]},\"meta\":{\"eip\":0,\"noavail\":false,\"searchId\":\"U0FMMTAxWTEwMDB8S0lWTE9OMjAyMS0wMy0yNQ==\",\"lang\":\"en\",\"rank\":5.9333334,\"cheapest\":false,\"fastest\":false,\"best\":false,\"bags\":1,\"country\":\"us\"},\"price\":326.9,\"originRate\":1,\"stops\":[2],\"time\":[{\"departure\":\"2021-03-25 05:25\",\"arrival\":\"2021-03-25 21:45\"}],\"bagFilter\":1,\"airportChange\":false,\"technicalStopCnt\":0,\"duration\":[1100],\"totalDuration\":1100,\"topCriteria\":\"\",\"rank\":5.9333334}",
                    "fq_json_booking": null,
                    "fq_ticket_json": null,
                    "itineraryDump": [
                        "1  AF6602E  25MAR  KIVOTP    525A    640A  TH OPERATED BY RO",
                        "2  AF1889E  25MAR  OTPCDG    225P    435P  TH",
                        "3  AF1380E  25MAR  CDGLHR    920P    945P  TH"
                    ],
                    "booking_id": "1",
                    "fq_type_name": "Base",
                    "fq_fare_type_name": "Public",
                    "fareType": "PUB",
                    "flight": {
                        "fl_product_id": 44,
                        "fl_trip_type_id": 1,
                        "fl_cabin_class": "E",
                        "fl_adults": 1,
                        "fl_children": 0,
                        "fl_infants": 0,
                        "fl_trip_type_name": "One Way",
                        "fl_cabin_class_name": "Economy"
                    },
                    "trips": [
                        {
                            "uid": "fqt6047ae8cde4af",
                            "key": null,
                            "duration": 1100,
                            "segments": [
                                {
                                    "uid": "fqs6047ae8cdf8d9",
                                    "departureTime": "2021-03-25 05:25",
                                    "arrivalTime": "2021-03-25 06:40",
                                    "flightNumber": 6602,
                                    "bookingClass": "E",
                                    "duration": 75,
                                    "departureAirportCode": "KIV",
                                    "departureAirportTerminal": "",
                                    "arrivalAirportCode": "OTP",
                                    "arrivalAirportTerminal": "",
                                    "operatingAirline": "RO",
                                    "marketingAirline": "AF",
                                    "airEquipType": "AT7",
                                    "marriageGroup": "I",
                                    "cabin": "E",
                                    "meal": "",
                                    "fareCode": "ES50BBST",
                                    "mileage": 215,
                                    "departureLocation": "Chisinau",
                                    "arrivalLocation": "Bucharest",
                                    "stop": 1,
                                    "stops": [
                                        {
                                            "qss_quote_segment_id": 9,
                                            "locationCode": "SCL",
                                            "equipment": "",
                                            "elapsedTime": 120,
                                            "duration": 120,
                                            "departureDateTime": "2021-09-09 00:00",
                                            "arrivalDateTime": "2021-09-08 00:00"
                                        }
                                    ],
                                    "baggage": [
                                        {
                                            "qsb_flight_pax_code_id": 1,
                                            "qsb_flight_quote_segment_id": 9,
                                            "qsb_airline_code": null,
                                            "qsb_carry_one": 1,
                                            "qsb_allow_pieces": 1,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null
                                        }
                                    ]
                                },
                                {
                                    "uid": "fqs6047ae8ce16d5",
                                    "departureTime": "2021-03-25 14:25",
                                    "arrivalTime": "2021-03-25 16:35",
                                    "flightNumber": 1889,
                                    "bookingClass": "E",
                                    "duration": 190,
                                    "departureAirportCode": "OTP",
                                    "departureAirportTerminal": "",
                                    "arrivalAirportCode": "CDG",
                                    "arrivalAirportTerminal": "2E",
                                    "operatingAirline": "AF",
                                    "marketingAirline": "AF",
                                    "airEquipType": "319",
                                    "marriageGroup": "I",
                                    "cabin": "E",
                                    "meal": "",
                                    "fareCode": "ES50BBST",
                                    "mileage": 1147,
                                    "departureLocation": "Bucharest",
                                    "arrivalLocation": "Paris",
                                    "stop": 0,
                                    "stops": [],
                                    "baggage": [
                                        {
                                            "qsb_flight_pax_code_id": 1,
                                            "qsb_flight_quote_segment_id": 10,
                                            "qsb_airline_code": null,
                                            "qsb_carry_one": 1,
                                            "qsb_allow_pieces": 1,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null
                                        }
                                    ]
                                },
                                {
                                    "uid": "fqs6047ae8ce248c",
                                    "departureTime": "2021-03-25 21:20",
                                    "arrivalTime": "2021-03-25 21:45",
                                    "flightNumber": 1380,
                                    "bookingClass": "E",
                                    "duration": 85,
                                    "departureAirportCode": "CDG",
                                    "departureAirportTerminal": "2E",
                                    "arrivalAirportCode": "LHR",
                                    "arrivalAirportTerminal": "2",
                                    "operatingAirline": "AF",
                                    "marketingAirline": "AF",
                                    "airEquipType": "318",
                                    "marriageGroup": "O",
                                    "cabin": "E",
                                    "meal": "",
                                    "fareCode": "ES50BBST",
                                    "mileage": 214,
                                    "departureLocation": "Paris",
                                    "arrivalLocation": "London",
                                    "stop": 0,
                                    "stops": [],
                                    "baggage": [
                                        {
                                            "qsb_flight_pax_code_id": 1,
                                            "qsb_flight_quote_segment_id": 11,
                                            "qsb_airline_code": null,
                                            "qsb_carry_one": 1,
                                            "qsb_allow_pieces": 1,
                                            "qsb_allow_weight": null,
                                            "qsb_allow_unit": null,
                                            "qsb_allow_max_weight": null,
                                            "qsb_allow_max_size": null
                                        }
                                    ]
                                }
                            ]
                        }
                    ],
                    "pax_prices": [
                        {
                            "qpp_fare": "271.00",
                            "qpp_tax": "55.90",
                            "qpp_system_mark_up": "0.00",
                            "qpp_agent_mark_up": "89.00",
                            "qpp_origin_fare": "271.00",
                            "qpp_origin_currency": "USD",
                            "qpp_origin_tax": "55.90",
                            "qpp_client_currency": "USD",
                            "qpp_client_fare": "271.00",
                            "qpp_client_tax": "55.90",
                            "paxType": "ADT"
                        }
                    ],
                    "paxes": [
                        {
                            "fp_uid": "fp604741cd064a1",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        },
                        {
                            "fp_uid": "fp6047ae79a875c",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        },
                        {
                            "fp_uid": "fp6047ae8cdbb37",
                            "fp_pax_id": null,
                            "fp_pax_type": "ADT",
                            "fp_first_name": null,
                            "fp_last_name": null,
                            "fp_middle_name": null,
                            "fp_dob": null
                        }
                    ]
                },
                "involuntary_change": {
                    "refundAllowed": false
                }
            },
            "quote_list": [
                {
                    "relation_type": "Voluntary Exchange",
                    "relation_type_id": 5, "(1-replace, 2-clone, 3-alternative, 4-reProtection, 5-voluntary exchange)"
                    "recommended": true,
                    "pq_gid": "289ddd4b911e88d7bf1eb14be44754d7",
                    "pq_name": "test",
                    "pq_order_id": 35,
                    "pq_description": null,
                    "pq_status_id": 1,
                    "pq_price": 0,
                    "pq_origin_price": 0,
                    "pq_client_price": 0,
                    "pq_service_fee_sum": 0,
                    "pq_origin_currency": null,
                    "pq_client_currency": "USD",
                    "pq_status_name": "New",
                    "pq_files": [],
                    "data": {
                        "changePricing" : {
                            "baseFare": 10.01,
                            "baseTax": 10.01,
                            "markup": 10.01,
                            "price": 30.01
                        },
                        "fq_flight_id": 2,
                        "fq_source_id": null,
                        "fq_product_quote_id": 191,
                        "gds": "S",
                        "pcc": "8KI0",
                        "fq_gds_offer_id": null,
                        "fq_type_id": 3,
                        "fq_cabin_class": "E",
                        "fq_trip_type_id": 1,
                        "validatingCarrier": "PR",
                        "fq_fare_type_id": 2,
                        "fq_last_ticket_date": null,
                        "fq_origin_search_data": "{\"gds\":\"S\",\"pcc\":\"8KI0\",\"trips\":[{\"duration\":848,\"segments\":[{\"meal\":null,\"stop\":0,\"cabin\":\"Y\",\"stops\":[],\"baggage\":[],\"brandId\":null,\"mileage\":null,\"duration\":600,\"fareCode\":null,\"arrivalTime\":\"2021-06-11 07:30:00\",\"airEquipType\":null,\"bookingClass\":\"E\",\"flightNumber\":\"8727\",\"departureTime\":\"2021-06-10 21:30:00\",\"marriageGroup\":\"\",\"marketingAirline\":\"DL\",\"operatingAirline\":null,\"arrivalAirportCode\":\"CDG\",\"departureAirportCode\":\"ROB\",\"arrivalAirportTerminal\":null,\"departureAirportTerminal\":null},{\"meal\":null,\"stop\":0,\"cabin\":\"Y\",\"stops\":[],\"baggage\":[],\"brandId\":null,\"mileage\":null,\"duration\":160,\"fareCode\":null,\"arrivalTime\":\"2021-06-11 12:55:00\",\"airEquipType\":null,\"bookingClass\":\"E\",\"flightNumber\":\"8395\",\"departureTime\":\"2021-06-11 10:15:00\",\"marriageGroup\":\"\",\"marketingAirline\":\"DL\",\"operatingAirline\":null,\"arrivalAirportCode\":\"LAX\",\"departureAirportCode\":\"CDG\",\"arrivalAirportTerminal\":null,\"departureAirportTerminal\":null},{\"meal\":null,\"stop\":0,\"cabin\":\"Y\",\"stops\":[],\"baggage\":[],\"brandId\":null,\"mileage\":null,\"duration\":88,\"fareCode\":null,\"arrivalTime\":\"2021-06-11 19:14:00\",\"airEquipType\":null,\"bookingClass\":\"E\",\"flightNumber\":\"3580\",\"departureTime\":\"2021-06-11 17:46:00\",\"marriageGroup\":\"\",\"marketingAirline\":\"DL\",\"operatingAirline\":null,\"arrivalAirportCode\":\"SMF\",\"departureAirportCode\":\"LAX\",\"arrivalAirportTerminal\":null,\"departureAirportTerminal\":null}]},{\"duration\":1233,\"segments\":[{\"meal\":null,\"stop\":0,\"cabin\":\"Y\",\"stops\":[],\"baggage\":[],\"brandId\":null,\"mileage\":null,\"duration\":127,\"fareCode\":null,\"arrivalTime\":\"2021-09-10 12:34\",\"airEquipType\":\"E7W\",\"bookingClass\":\"E\",\"flightNumber\":\"3864\",\"departureTime\":\"2021-09-10 10:27\",\"marriageGroup\":\"\",\"marketingAirline\":\"DL\",\"operatingAirline\":null,\"arrivalAirportCode\":\"SEA\",\"departureAirportCode\":\"SMF\",\"arrivalAirportTerminal\":null,\"departureAirportTerminal\":null},{\"meal\":null,\"stop\":0,\"cabin\":\"Y\",\"stops\":[],\"baggage\":[],\"brandId\":null,\"mileage\":null,\"duration\":201,\"fareCode\":null,\"arrivalTime\":\"2021-09-10 13:34\",\"airEquipType\":\"739\",\"bookingClass\":\"E\",\"flightNumber\":\"759\",\"departureTime\":\"2021-09-10 08:13\",\"marriageGroup\":\"\",\"marketingAirline\":\"DL\",\"operatingAirline\":null,\"arrivalAirportCode\":\"MSP\",\"departureAirportCode\":\"SEA\",\"arrivalAirportTerminal\":null,\"departureAirportTerminal\":null},{\"meal\":null,\"stop\":0,\"cabin\":\"Y\",\"stops\":[],\"baggage\":[],\"brandId\":null,\"mileage\":null,\"duration\":510,\"fareCode\":null,\"arrivalTime\":\"2021-09-11 08:15\",\"airEquipType\":\"333\",\"bookingClass\":\"E\",\"flightNumber\":\"42\",\"departureTime\":\"2021-09-10 16:45\",\"marriageGroup\":\"\",\"marketingAirline\":\"DL\",\"operatingAirline\":null,\"arrivalAirportCode\":\"CDG\",\"departureAirportCode\":\"MSP\",\"arrivalAirportTerminal\":null,\"departureAirportTerminal\":null},{\"meal\":null,\"stop\":1,\"cabin\":\"Y\",\"stops\":[{\"duration\":85,\"equipment\":null,\"elapsedTime\":null,\"locationCode\":\"BKO\",\"arrivalDateTime\":\"2021-09-11 13:55\",\"departureDateTime\":\"2021-09-11 15:20\"}],\"baggage\":[],\"brandId\":null,\"mileage\":null,\"duration\":395,\"fareCode\":null,\"arrivalTime\":\"2021-09-11 16:50\",\"airEquipType\":\"359\",\"bookingClass\":\"E\",\"flightNumber\":\"7351\",\"departureTime\":\"2021-09-11 10:15\",\"marriageGroup\":\"\",\"marketingAirline\":\"DL\",\"operatingAirline\":null,\"arrivalAirportCode\":\"ROB\",\"departureAirportCode\":\"CDG\",\"arrivalAirportTerminal\":null,\"departureAirportTerminal\":null}]}],\"fareType\":\"SR\",\"itineraryDump\":[\"DL8727E 10JUN ROBCDG TK  930P  730A+ 11JUN TH\/FR\",\"DL8395E 11JUN CDGLAX HK 1015A 1255P FR\",\"DL3580E 11JUN LAXSMF HK  546P  714P FR\",\"DL3864E 10SEP SMFSEA TK 1027A 1234P FR\",\"DL 759E 10SEP SEAMSP TK  813A  134P FR\",\"DL  42E 10SEP MSPCDG TK  445P  815A+ 11SEP FR\/SA\",\"DL7351E 11SEP CDGROB HK 1015A  450P SA\",\"DL7351E 11SEP BKOROB HK  320P  450P SA\"],\"validatingCarrier\":\"PR\"}",
                        "fq_json_booking": null,
                        "fq_ticket_json": null,
                        "itineraryDump": [
                            "1  DL8727E  10JUN  ROBCDG    930P    730A+  11JUN  TH/FR",
                            "2  DL8395E  11JUN  CDGLAX  1015A  1255P  FR",
                            "3  DL3580E  11JUN  LAXSMF    546P    714P  FR",
                            "4  DL3864E  10SEP  SMFSEA  1027A  1234P  FR",
                            "5  DL  759E  10SEP  SEAMSP    813A    134P  FR",
                            "6  DL    42E  10SEP  MSPCDG    445P    815A+  11SEP  FR/SA",
                            "7  DL7351E  11SEP  CDGROB  1015A    450P  SA"
                        ],
                        "booking_id": "1",
                        "fq_type_name": "ReProtection",
                        "fq_fare_type_name": "Private",
                        "fareType": "SR",
                        "flight": {
                            "fl_product_id": 44,
                            "fl_trip_type_id": 1,
                            "fl_cabin_class": "E",
                            "fl_adults": 1,
                            "fl_children": 0,
                            "fl_infants": 0,
                            "fl_trip_type_name": "One Way",
                            "fl_cabin_class_name": "Economy"
                        },
                        "trips": [
                            {
                                "uid": "fqt6116010ce3d6b",
                                "key": null,
                                "duration": 848,
                                "segments": [
                                    {
                                        "uid": "fqs6116010ce9306",
                                        "departureTime": "2021-06-10 21:30",
                                        "arrivalTime": "2021-06-11 07:30",
                                        "flightNumber": 8727,
                                        "bookingClass": "E",
                                        "duration": 600,
                                        "departureAirportCode": "ROB",
                                        "departureAirportTerminal": "",
                                        "arrivalAirportCode": "CDG",
                                        "arrivalAirportTerminal": "",
                                        "operatingAirline": "",
                                        "marketingAirline": "DL",
                                        "airEquipType": "",
                                        "marriageGroup": "",
                                        "cabin": "E",
                                        "meal": "",
                                        "fareCode": "",
                                        "mileage": null,
                                        "departureLocation": "Monrovia",
                                        "arrivalLocation": "Paris",
                                        "stop": 0,
                                        "stops": []
                                    },
                                    {
                                        "uid": "fqs6116010ceb91e",
                                        "departureTime": "2021-06-11 10:15",
                                        "arrivalTime": "2021-06-11 12:55",
                                        "flightNumber": 8395,
                                        "bookingClass": "E",
                                        "duration": 160,
                                        "departureAirportCode": "CDG",
                                        "departureAirportTerminal": "",
                                        "arrivalAirportCode": "LAX",
                                        "arrivalAirportTerminal": "",
                                        "operatingAirline": "",
                                        "marketingAirline": "DL",
                                        "airEquipType": "",
                                        "marriageGroup": "",
                                        "cabin": "E",
                                        "meal": "",
                                        "fareCode": "",
                                        "mileage": null,
                                        "departureLocation": "Paris",
                                        "arrivalLocation": "Los Angeles",
                                        "stop": 0,
                                        "stops": [],
                                        "baggage": []
                                    },
                                    {
                                        "uid": "fqs6116010cebd9a",
                                        "departureTime": "2021-06-11 17:46",
                                        "arrivalTime": "2021-06-11 19:14",
                                        "flightNumber": 3580,
                                        "bookingClass": "E",
                                        "duration": 88,
                                        "departureAirportCode": "LAX",
                                        "departureAirportTerminal": "",
                                        "arrivalAirportCode": "SMF",
                                        "arrivalAirportTerminal": "",
                                        "operatingAirline": "",
                                        "marketingAirline": "DL",
                                        "airEquipType": "",
                                        "marriageGroup": "",
                                        "cabin": "E",
                                        "meal": "",
                                        "fareCode": "",
                                        "mileage": null,
                                        "departureLocation": "Los Angeles",
                                        "arrivalLocation": "Sacramento",
                                        "stop": 0,
                                        "stops": [],
                                        "baggage": []
                                    }
                                ]
                            },
                            {
                                "uid": "fqt6116010cec0cf",
                                "key": null,
                                "duration": 1233,
                                "segments": [
                                    {
                                        "uid": "fqs6116010cec45b",
                                        "departureTime": "2021-09-10 10:27",
                                        "arrivalTime": "2021-09-10 12:34",
                                        "flightNumber": 3864,
                                        "bookingClass": "E",
                                        "duration": 127,
                                        "departureAirportCode": "SMF",
                                        "departureAirportTerminal": "",
                                        "arrivalAirportCode": "SEA",
                                        "arrivalAirportTerminal": "",
                                        "operatingAirline": "",
                                        "marketingAirline": "DL",
                                        "airEquipType": "E7W",
                                        "marriageGroup": "",
                                        "cabin": "E",
                                        "meal": "",
                                        "fareCode": "",
                                        "mileage": null,
                                        "departureLocation": "Sacramento",
                                        "arrivalLocation": "Seattle",
                                        "stop": 0,
                                        "stops": []
                                    },
                                    {
                                        "uid": "fqs6116010cec885",
                                        "departureTime": "2021-09-10 08:13",
                                        "arrivalTime": "2021-09-10 13:34",
                                        "flightNumber": 759,
                                        "bookingClass": "E",
                                        "duration": 201,
                                        "departureAirportCode": "SEA",
                                        "departureAirportTerminal": "",
                                        "arrivalAirportCode": "MSP",
                                        "arrivalAirportTerminal": "",
                                        "operatingAirline": "",
                                        "marketingAirline": "DL",
                                        "airEquipType": "739",
                                        "marriageGroup": "",
                                        "cabin": "E",
                                        "meal": "",
                                        "fareCode": "",
                                        "mileage": null,
                                        "departureLocation": "Seattle",
                                        "arrivalLocation": "Minneapolis",
                                        "stop": 0,
                                        "stops": [],
                                        "baggage": []
                                    },
                                    {
                                        "uid": "fqs6116010ceccdb",
                                        "departureTime": "2021-09-10 16:45",
                                        "arrivalTime": "2021-09-11 08:15",
                                        "flightNumber": 42,
                                        "bookingClass": "E",
                                        "duration": 510,
                                        "departureAirportCode": "MSP",
                                        "departureAirportTerminal": "",
                                        "arrivalAirportCode": "CDG",
                                        "arrivalAirportTerminal": "",
                                        "operatingAirline": "",
                                        "marketingAirline": "DL",
                                        "airEquipType": "333",
                                        "marriageGroup": "",
                                        "cabin": "E",
                                        "meal": "",
                                        "fareCode": "",
                                        "mileage": null,
                                        "departureLocation": "Minneapolis",
                                        "arrivalLocation": "Paris",
                                        "stop": 0,
                                        "stops": [],
                                        "baggage": []
                                    },
                                    {
                                        "uid": "fqs6116010ced118",
                                        "departureTime": "2021-09-11 10:15",
                                        "arrivalTime": "2021-09-11 16:50",
                                        "flightNumber": 7351,
                                        "bookingClass": "E",
                                        "duration": 395,
                                        "departureAirportCode": "CDG",
                                        "departureAirportTerminal": "",
                                        "arrivalAirportCode": "ROB",
                                        "arrivalAirportTerminal": "",
                                        "operatingAirline": "",
                                        "marketingAirline": "DL",
                                        "airEquipType": "359",
                                        "marriageGroup": "",
                                        "cabin": "E",
                                        "meal": "",
                                        "fareCode": "",
                                        "mileage": null,
                                        "departureLocation": "Paris",
                                        "arrivalLocation": "Monrovia",
                                        "stop": 1,
                                        "stops": [
                                            {
                                                "qss_quote_segment_id": 26,
                                                "locationCode": "BKO",
                                                "equipment": null,
                                                "elapsedTime": null,
                                                "duration": 85,
                                                "departureDateTime": "2021-09-11 15:20",
                                                "arrivalDateTime": "2021-09-11 13:55"
                                            }
                                        ],
                                        "baggage": []
                                    }
                                ]
                            }
                        ],
                        "pax_prices": [
                            {
                                "qpp_fare": "877.00",
                                "qpp_tax": "464.28",
                                "qpp_system_mark_up": "50.00",
                                "qpp_agent_mark_up": "0.00",
                                "qpp_origin_fare": null,
                                "qpp_origin_currency": "USD",
                                "qpp_origin_tax": null,
                                "qpp_client_currency": "USD",
                                "qpp_client_fare": null,
                                "qpp_client_tax": null,
                                "paxType": "ADT"
                            }
                        ],
                        "paxes": [
                            {
                                "fp_uid": "fp604741cd064a1",
                                "fp_pax_id": null,
                                "fp_pax_type": "ADT",
                                "fp_first_name": null,
                                "fp_last_name": null,
                                "fp_middle_name": null,
                                "fp_dob": null
                            }
                        ]
                    }
                }
            ],
            "last_change": {
                "pqc_id": 1,
                "pqc_pq_id": 645,
                "pqc_case_id": 135814,
                "pqc_decision_user": 464,
                "pqc_status_id": 6,
                "pqc_decision_type_id": 1,
                "pqc_created_dt": "2021-08-17 11:44:34",
                "pqc_updated_dt": "2021-08-26 10:09:03",
                "pqc_decision_dt": "2021-08-24 14:33:39",
                "pqc_is_automate": 0
            }
        }
     *
     * @apiErrorExample {json} Error-Response:
     * HTTP/1.1 200 Ok
     * {
            "status": 422,
            "message": "Product Quote not found",
            "errors": [
                "Product Quote not found"
            ],
        }
     *
     * @apiErrorExample {json} Error-Response (500):
     * HTTP/1.1 200 Ok
     * {
            "status": 500,
            "message": "Internal Server Error",
            "errors": []
        }
     *
     * @apiErrorExample {html} Note:
     * [
     *      In "quote_list" show by status restriction from settings - "exchange_quote_confirm_status_list"
     * ]
     */
    public function actionProductQuoteGet()
    {
        $form = new ProductQuoteGetForm();

        if (!$form->load(Yii::$app->request->get())) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found data on GET request'),
            );
        }

        if (!$form->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($form->getErrors()),
            );
        }

        try {
            $productQuote = $this->productQuoteRepository->findByGidFlightProductQuote($form->product_quote_gid);
            $responseProductQuote = $productQuote->toArray();

            if ($lastReProtection = ProductQuoteChangeService::lastActiveReProtection($productQuote->pq_id)) {
                $responseProductQuote['involuntary_change']['refundAllowed'] = (bool) $lastReProtection->pqc_refund_allowed;
            }

            $response = new SuccessResponse(
                new Message('product_quote', $responseProductQuote)
            );

            if ($form->withQuoteList()) {
                $quoteList = [];
                $relationQuotes = ProductQuoteRelation::find()
                    ->innerJoin(ProductQuote::tableName(), 'pqr_related_pq_id = pq_id')
                    ->leftJoinRecommended()
                    ->byParentQuoteId($productQuote->pq_id)
                    ->byType($form->getQuoteTypes())
                    ->orderByRecommendedDesc()
                    ->andWhere(['IN', 'pq_status_id', SettingHelper::getExchangeQuoteConfirmStatusList()])
                    ->all();

                foreach ($relationQuotes as $relationQuote) {
                    $changeProductQuote = $relationQuote->pqrRelatedPq;
                    $data = $changeProductQuote->toArray();
                    $data = ArrayHelper::merge([
                        'recommended' => $changeProductQuote->isRecommended(),
                        'relation_type' => ProductQuoteRelation::getTypeName($relationQuote->pqr_type_id),
                        'relation_type_id' => $relationQuote->pqr_type_id,
                    ], $data);

                    $data['data']['changePricing'] = null;
                    if ($relationQuote->pqr_type_id === ProductQuoteRelation::TYPE_VOLUNTARY_EXCHANGE) {
                        $data['data']['changePricing'] = FlightQuotePaxPriceHelper::calculateVoluntaryPricing($changeProductQuote);
                    }

                    $quoteList[] = $data;
                }
                $response->addMessage(
                    new Message('quote_list', $quoteList)
                );
            }

            if ($form->withLastChange() && $lastQuoteChange = $productQuote->productQuoteLastChange) {
                $response->addMessage(
                    new Message('last_change', $lastQuoteChange->toArray())
                );
            }

            return $response;
        } catch (NotFoundException | \RuntimeException $e) {
            return new ErrorResponse(
                new StatusCodeMessage(422),
                new MessageMessage($e->getMessage()),
                new ErrorsMessage($e->getMessage()),
                new CodeMessage($e->getCode())
            );
        } catch (\Throwable $e) {
            \Yii::error(AppHelper::throwableLog($e), 'API:FlightController:actionProductQuoteGet:Throwable');
            return new ErrorResponse(
                new StatusCodeMessage(500),
                new MessageMessage('Internal Server Error'),
                new CodeMessage($e->getCode())
            );
        }
    }

    /**
     * @api {post} /v2/flight/reprotection-exchange ReProtection exchange
     * @apiVersion 0.2.0
     * @apiName ReProtection Exchange
     * @apiGroup Flight
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{7..10}}    booking_id          Booking ID
     * @apiParam {string{100}}      [email]             Email
     * @apiParam {string{20}}       [phone]             Phone
     * @apiParam {object}           [flight_request]   Flight Request
     *
     * @apiParamExample {json} Request-Example:
     *  {
     *      "booking_id": "XXXYYYZ",
     *      "email": "example@mail.com",
     *      "phone": "+13736911111",
     *      "flight_request": {"exampleKey" : "exampleValue"}
     *  }
     *
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {
     *        "status": 200,
     *        "message": "OK",
     *        "data": {
     *            "success" => true,
     *            "warnings": []
     *        },
     *        "technical": {
     *           ...
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     *
     * @apiErrorExample {json} Error-Response:
     * HTTP/1.1 400 Bad Request
     * {
     *        "status": 400,
     *        "message": "Load data error",
     *        "errors": [
     *           "Not found data on POST request"
     *        ],
     *        "technical": {
     *           ...
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     *
     * @apiErrorExample {json} Error-Response:
     * HTTP/1.1 422 Unprocessable entity
     * {
     *        "status": 422,
     *        "message": "Validation error",
     *        "errors": [
     *            "type": [
     *               "Type cannot be blank."
     *             ]
     *        ],
     *        "technical": {
     *           ...
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     *
     * @apiErrorExample {json} Error-Response (422) Code 101:
     * HTTP/1.1 422 Error
     * {
     *        "status": 422,
     *        "message": "Error",
     *        "data": [
     *              "success": false,
     *              "error": "Product Quote Change status is not in \"pending\". Current status Canceled"
     *        ],
     *        "code": 101,
     *        "errors": [],
     *        "technical": {
     *           ...
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     */
    public function actionReprotectionExchange()
    {
        try {
            $post = Yii::$app->request->post();
        } catch (\Throwable $throwable) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::POST_DATA_ERROR),
                new ErrorsMessage($throwable->getMessage()),
            );
        }

        $reProtectionExchangeForm = new ReProtectionExchangeForm();
        if (!$reProtectionExchangeForm->load($post)) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found data on POST request'),
            );
        }
        if (!$reProtectionExchangeForm->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($reProtectionExchangeForm->getErrors()),
            );
        }

        try {
            $this->reProtectionExchangeService->handle($reProtectionExchangeForm);

            return new SuccessResponse(
                new DataMessage([
                    'success' => true,
                    'warnings' => $reProtectionExchangeForm->getWarnings()
                ])
            );
        } catch (\DomainException $exception) {
            \Yii::error([
                'message' => 'DomainException: Reprotection Exchange error',
                'error' => $exception->getMessage(),
                'request' => LogHelper::hidePersonalData($reProtectionExchangeForm->getAttributes(), ['email', 'phone'], 3),
            ], 'FlightController:reprotectionExchange:DomainException');

            return new ErrorResponse(
                new DataMessage([
                    'success' => false,
                    'error' => $exception->getMessage()
                ]),
                new CodeMessage($exception->getCode())
            );
        } catch (\Throwable $throwable) {
            $message = [
                'message' => $throwable->getMessage(),
                'request' => $post,
                'throwable' => AppHelper::throwableLog($throwable),
            ];
            if ($throwable instanceof DomainException) {
                Yii::warning($message, 'FlightController:actionReprotectionExchange:Warning');
            } else {
                Yii::error($message, 'FlightController:actionReprotectionExchange:Error');
            }
            return new ErrorResponse(
                new DataMessage([
                    'success' => false,
                    'error' => $throwable->getMessage(),
                ])
            );
        }
    }
}

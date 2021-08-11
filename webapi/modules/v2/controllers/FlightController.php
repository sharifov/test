<?php

namespace webapi\modules\v2\controllers;

use common\components\jobs\ReprotectionCreateJob;
use modules\flight\models\FlightRequest;
use modules\flight\src\repositories\flightRequest\FlightRequestRepository;
use modules\flight\src\useCases\reprotectionCreate\form\ReprotectionCreateForm;
use modules\flight\src\useCases\reprotectionCreate\form\ReprotectionGetForm;
use sales\helpers\app\AppHelper;
use sales\repositories\NotFoundException;
use sales\repositories\product\ProductQuoteRepository;
use sales\services\TransactionManager;
use webapi\src\logger\ApiLogger;
use webapi\src\logger\behaviors\filters\creditCard\CreditCardFilter;
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
use webapi\src\response\messages\StatusFailedMessage;
use webapi\src\response\SuccessResponse;
use Yii;
use yii\helpers\ArrayHelper;
use modules\flight\src\useCases\reprotectionDecision;

/**
 * Class FlightController
 *
 * @property TransactionManager $transactionManager
 * @property-read ProductQuoteRepository $productQuoteRepository
 */
class FlightController extends BaseController
{
    private TransactionManager $transactionManager;
    /**
     * @var ProductQuoteRepository
     */
    private ProductQuoteRepository $productQuoteRepository;

    /**
     * @param $id
     * @param $module
     * @param ApiLogger $logger
     * @param TransactionManager $transactionManager
     * @param ProductQuoteRepository $productQuoteRepository
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        ApiLogger $logger,
        TransactionManager $transactionManager,
        ProductQuoteRepository $productQuoteRepository,
        $config = []
    ) {
        $this->transactionManager = $transactionManager;
        $this->productQuoteRepository = $productQuoteRepository;
        parent::__construct($id, $module, $logger, $config);
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['logger'] = [
            'class' => SimpleLoggerBehavior::class,
            'except' => ['reprotection-get'],
        ];
        $behaviors['request'] = [
            'class' => RequestBehavior::class,
            'except' => ['reprotection-get'],
        ];
        $behaviors['responseStatusCode'] = [
            'class' => ResponseStatusCodeBehavior::class,
            'except' => ['reprotection-get'],
        ];
        $behaviors['technical'] = [
            'class' => TechnicalInfoBehavior::class,
            'except' => ['reprotection-get'],
        ];
        return $behaviors;
    }

    protected function verbs(): array
    {
        return ArrayHelper::merge(
            parent::verbs(),
            [
                'reprotection-get' => ['GET']
            ]
        );
    }

    /**
     * @api {post} /v2/flight/reprotection-create Create flight reprotection from BO
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
     * @apiParam {string{10}}           booking_id                  Booking Id
     * @apiParam {string{50}}           project_key                 Project key
     * @apiParam {bool}                 [is_automate]               Is automate (default false)
     * @apiParam {object}               [flight_quote]              Flight quote
     *
     * @apiParamExample {json} Request-Example:
     *   {
     *      "booking_id": "XXXYYYZ",
     *      "is_automate": false,
     *      "flight_quote": {
                "gds": "S",
                "key": "2_T1ZBMTAxKlkxMDAwL0xBWFRQRTIwMjEtMDUtMTMvVFBFTEFYMjAyMS0wNi0yMCpQUn4jUFIxMDMjUFI4OTAjUFI4OTEjUFIxMDJ+bGM6ZW5fdXM=",
                "pcc": "8KI0",
                "cons": "GTT",
                "keys": {
                    "services": {
                        "support": {
                            "amount": 75
                        }
                    },
                    "seatHoldSeg": {
                        "trip": 0,
                        "seats": 9,
                        "segment": 0
                    },
                    "verification": {
                        "headers": {
                            "X-Client-Ip": "92.115.180.30",
                            "X-Kiv-Cust-Ip": "92.115.180.30",
                            "X-Kiv-Cust-ipv": "0",
                            "X-Kiv-Cust-ssid": "ovago-dev-0484692",
                            "X-Kiv-Cust-direct": "true",
                            "X-Kiv-Cust-browser": "desktop"
                        }
                    }
                },
                "meta": {
                    "eip": 0,
                    "bags": 2,
                    "best": false,
                    "lang": "en",
                    "rank": 6,
                    "group1": "LAXTPE:PRPR:0:TPELAX:PRPR:0:767.75",
                    "country": "us",
                    "fastest": false,
                    "noavail": false,
                    "cheapest": true,
                    "searchId": "T1ZBMTAxWTEwMDB8TEFYVFBFMjAyMS0wNS0xM3xUUEVMQVgyMDIxLTA2LTIw"
                },
                "cabin": "Y",
                "trips": [
                    {
                        "tripId": 1,
                        "duration": 1150,
                        "segments": [
                            {
                                "meal": "D",
                                "stop": 0,
                                "cabin": "Y",
                                "stops": [],
                                "baggage": {
                                    "ADT": {
                                        "carryOn": true,
                                        "airlineCode": "PR",
                                        "allowPieces": 2,
                                        "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
                                        "allowMaxWeight": "UP TO 50 POUNDS/23 KILOGRAMS"
                                    }
                                },
                                "mileage": 7305,
                                "duration": 870,
                                "fareCode": "U9XBUS",
                                "segmentId": 1,
                                "arrivalTime": "2021-05-15 04:00",
                                "airEquipType": "773",
                                "bookingClass": "U",
                                "flightNumber": "103",
                                "departureTime": "2021-05-13 22:30",
                                "marriageGroup": "O",
                                "recheckBaggage": false,
                                "marketingAirline": "PR",
                                "operatingAirline": "PR",
                                "arrivalAirportCode": "MNL",
                                "departureAirportCode": "LAX",
                                "arrivalAirportTerminal": "2",
                                "departureAirportTerminal": "B"
                            },
                            {
                                "meal": "B",
                                "stop": 0,
                                "cabin": "Y",
                                "stops": [],
                                "baggage": {
                                    "ADT": {
                                        "carryOn": true,
                                        "airlineCode": "PR",
                                        "allowPieces": 2,
                                        "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
                                        "allowMaxWeight": "UP TO 50 POUNDS/23 KILOGRAMS"
                                    }
                                },
                                "mileage": 728,
                                "duration": 130,
                                "fareCode": "U9XBUS",
                                "segmentId": 2,
                                "arrivalTime": "2021-05-15 08:40",
                                "airEquipType": "321",
                                "bookingClass": "U",
                                "flightNumber": "890",
                                "departureTime": "2021-05-15 06:30",
                                "marriageGroup": "I",
                                "recheckBaggage": false,
                                "marketingAirline": "PR",
                                "operatingAirline": "PR",
                                "arrivalAirportCode": "TPE",
                                "departureAirportCode": "MNL",
                                "arrivalAirportTerminal": "1",
                                "departureAirportTerminal": "1"
                            }
                        ]
                    }
                ],
                "paxCnt": 1,
                "prices": {
                    "comm": 0,
                    "isCk": false,
                    "ccCap": 16.900002,
                    "markup": 50,
                    "oMarkup": {
                        "amount": 50,
                        "currency": "USD"
                    },
                    "markupId": 8833,
                    "totalTax": 321.75,
                    "markupUid": "1c7afe8c-a34f-434e-8fa3-87b9b7b1ff4e",
                    "totalPrice": 767.75,
                    "lastTicketDate": "2021-03-31"
                },
                "currency": "USD",
                "fareType": "SR",
                "maxSeats": 9,
                "tripType": "RT",
                "penalties": {
                    "list": [
                        {
                            "type": "re",
                            "permitted": false,
                            "applicability": "before"
                        },
                        {
                            "type": "re",
                            "permitted": false,
                            "applicability": "after"
                        },
                        {
                            "type": "ex",
                            "amount": 425,
                            "oAmount": {
                                "amount": 425,
                                "currency": "USD"
                            },
                            "permitted": true,
                            "applicability": "before"
                        },
                        {
                            "type": "ex",
                            "amount": 425,
                            "oAmount": {
                                "amount": 425,
                                "currency": "USD"
                            },
                            "permitted": true,
                            "applicability": "after"
                        }
                    ],
                    "refund": false,
                    "exchange": true
                },
                "routingId": 1,
                "currencies": [
                    "USD"
                ],
                "founded_dt": "2021-02-25 13:44:54.570",
                "passengers": {
                    "ADT": {
                        "cnt": 1,
                        "tax": 321.75,
                        "comm": 0,
                        "ccCap": 16.900002,
                        "price": 767.75,
                        "codeAs": "JCB",
                        "markup": 50,
                        "occCap": {
                            "amount": 16.900002,
                            "currency": "USD"
                        },
                        "baseTax": 271.75,
                        "oMarkup": {
                            "amount": 50,
                            "currency": "USD"
                        },
                        "baseFare": 446,
                        "oBaseTax": {
                            "amount": 271.75,
                            "currency": "USD"
                        },
                        "oBaseFare": {
                            "amount": 446,
                            "currency": "USD"
                        },
                        "pubBaseFare": 446
                    }
                },
                "ngsFeatures": {
                    "list": null,
                    "name": "",
                    "stars": 3
                },
                "currencyRates": {
                    "CADUSD": {
                        "to": "USD",
                        "from": "CAD",
                        "rate": 0.78417
                    },
                    "DKKUSD": {
                        "to": "USD",
                        "from": "DKK",
                        "rate": 0.16459
                    },
                    "EURUSD": {
                        "to": "USD",
                        "from": "EUR",
                        "rate": 1.23967
                    },
                    "GBPUSD": {
                        "to": "USD",
                        "from": "GBP",
                        "rate": 1.37643
                    },
                    "KRWUSD": {
                        "to": "USD",
                        "from": "KRW",
                        "rate": 0.00091
                    },
                    "MYRUSD": {
                        "to": "USD",
                        "from": "MYR",
                        "rate": 0.25006
                    },
                    "SEKUSD": {
                        "to": "USD",
                        "from": "SEK",
                        "rate": 0.12221
                    },
                    "TWDUSD": {
                        "to": "USD",
                        "from": "TWD",
                        "rate": 0.03592
                    },
                    "USDCAD": {
                        "to": "CAD",
                        "from": "USD",
                        "rate": 1.30086
                    },
                    "USDDKK": {
                        "to": "DKK",
                        "from": "USD",
                        "rate": 6.19797
                    },
                    "USDEUR": {
                        "to": "EUR",
                        "from": "USD",
                        "rate": 0.83926
                    },
                    "USDGBP": {
                        "to": "GBP",
                        "from": "USD",
                        "rate": 0.75587
                    },
                    "USDKRW": {
                        "to": "KRW",
                        "from": "USD",
                        "rate": 1117.1008
                    },
                    "USDMYR": {
                        "to": "MYR",
                        "from": "USD",
                        "rate": 4.07943
                    },
                    "USDSEK": {
                        "to": "SEK",
                        "from": "USD",
                        "rate": 8.34736
                    },
                    "USDTWD": {
                        "to": "TWD",
                        "from": "USD",
                        "rate": 28.96525
                    },
                    "USDUSD": {
                        "to": "USD",
                        "from": "USD",
                        "rate": 1
                    }
                },
                "validatingCarrier": "PR"
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
        $post = Yii::$app->request->post();
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

        if ($reprotectionCreateForm->is_automate && empty($reprotectionCreateForm->flight_quote)) {
            $reprotectionCreateForm->is_automate = false;
            $dataMessage['warning'] = '"is_automate" parameter set to FALSE because "flight_quote" is empty';
        }

        try {
            $apiUserId = $this->auth->getId();
            $resultId = $this->transactionManager->wrap(function () use ($reprotectionCreateForm, $apiUserId, $post) {
                $flightRequest = FlightRequest::create(
                    $reprotectionCreateForm->booking_id,
                    FlightRequest::TYPE_REPRODUCTION_CREATE,
                    $post,
                    $reprotectionCreateForm->getProject()->id,
                    $apiUserId
                );
                $flightRequest = (new FlightRequestRepository())->save($flightRequest);

                $job = new ReprotectionCreateJob();
                $job->flight_request_id = $flightRequest->fr_id;
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
                    "fq_gds": "T",
                    "fq_gds_pcc": "E9V",
                    "fq_gds_offer_id": null,
                    "fq_type_id": 0,
                    "fq_cabin_class": "E",
                    "fq_trip_type_id": 3,
                    "fq_main_airline": "OS",
                    "fq_fare_type_id": 1,
                    "fq_origin_search_data": "{\"key\":\"2_U0FMMTAxKlkyMTAwL0tJVkxPTjIwMjItMDEtMTIvTE9ORlJBMjAyMi0wMS0xNS9GUkFLSVYyMDIyLTAxLTI0Kk9TfiNPUzY1NiNPUzQ1NSNMSDkwNSNMSDE0NzR+bGM6ZW5fdXM=\",\"routingId\":1,\"prices\":{\"lastTicketDate\":\"2021-07-31\",\"totalPrice\":1414.4,\"totalTax\":872.4,\"comm\":0,\"isCk\":false,\"markupId\":0,\"markupUid\":\"\",\"markup\":0},\"passengers\":{\"ADT\":{\"codeAs\":\"ADT\",\"cnt\":2,\"baseFare\":197,\"pubBaseFare\":197,\"baseTax\":296.8,\"markup\":0,\"comm\":0,\"price\":493.8,\"tax\":296.8,\"oBaseFare\":{\"amount\":197,\"currency\":\"USD\"},\"oBaseTax\":{\"amount\":296.8,\"currency\":\"USD\"}},\"CHD\":{\"codeAs\":\"CHD\",\"cnt\":1,\"baseFare\":148,\"pubBaseFare\":148,\"baseTax\":278.8,\"markup\":0,\"comm\":0,\"price\":426.8,\"tax\":278.8,\"oBaseFare\":{\"amount\":148,\"currency\":\"USD\"},\"oBaseTax\":{\"amount\":278.8,\"currency\":\"USD\"}}},\"penalties\":{\"exchange\":true,\"refund\":false,\"list\":[{\"type\":\"ex\",\"applicability\":\"before\",\"permitted\":true,\"amount\":0},{\"type\":\"ex\",\"applicability\":\"after\",\"permitted\":true,\"amount\":0},{\"type\":\"re\",\"applicability\":\"before\",\"permitted\":false},{\"type\":\"re\",\"applicability\":\"after\",\"permitted\":false}]},\"trips\":[{\"tripId\":1,\"segments\":[{\"segmentId\":1,\"departureTime\":\"2022-01-12 16:00\",\"arrivalTime\":\"2022-01-12 16:45\",\"stop\":0,\"stops\":[],\"flightNumber\":\"656\",\"bookingClass\":\"K\",\"duration\":105,\"departureAirportCode\":\"KIV\",\"departureAirportTerminal\":\"\",\"arrivalAirportCode\":\"VIE\",\"arrivalAirportTerminal\":\"3\",\"operatingAirline\":\"OS\",\"airEquipType\":\"E95\",\"marketingAirline\":\"OS\",\"marriageGroup\":\"I\",\"mileage\":583,\"cabin\":\"Y\",\"meal\":\"\",\"fareCode\":\"K03CLSE8\",\"baggage\":{\"ADT\":{\"carryOn\":true,\"allowPieces\":1},\"CHD\":{\"carryOn\":true,\"allowPieces\":1}},\"recheckBaggage\":false},{\"segmentId\":2,\"departureTime\":\"2022-01-12 17:15\",\"arrivalTime\":\"2022-01-12 18:40\",\"stop\":0,\"stops\":[],\"flightNumber\":\"455\",\"bookingClass\":\"K\",\"duration\":145,\"departureAirportCode\":\"VIE\",\"departureAirportTerminal\":\"3\",\"arrivalAirportCode\":\"LHR\",\"arrivalAirportTerminal\":\"2\",\"operatingAirline\":\"OS\",\"airEquipType\":\"321\",\"marketingAirline\":\"OS\",\"marriageGroup\":\"O\",\"mileage\":774,\"cabin\":\"Y\",\"meal\":\"\",\"fareCode\":\"K03CLSE8\",\"baggage\":{\"ADT\":{\"carryOn\":true,\"allowPieces\":1},\"CHD\":{\"carryOn\":true,\"allowPieces\":1}},\"recheckBaggage\":false}],\"duration\":280},{\"tripId\":2,\"segments\":[{\"segmentId\":1,\"departureTime\":\"2022-01-15 11:30\",\"arrivalTime\":\"2022-01-15 14:05\",\"stop\":0,\"stops\":[],\"flightNumber\":\"905\",\"bookingClass\":\"Q\",\"duration\":95,\"departureAirportCode\":\"LHR\",\"departureAirportTerminal\":\"2\",\"arrivalAirportCode\":\"FRA\",\"arrivalAirportTerminal\":\"1\",\"operatingAirline\":\"LH\",\"airEquipType\":\"32N\",\"marketingAirline\":\"LH\",\"marriageGroup\":\"O\",\"mileage\":390,\"cabin\":\"Y\",\"meal\":\"\",\"fareCode\":\"Q03CLSE0\",\"baggage\":{\"ADT\":{\"carryOn\":true,\"allowPieces\":1},\"CHD\":{\"carryOn\":true,\"allowPieces\":1}},\"recheckBaggage\":false}],\"duration\":95},{\"tripId\":3,\"segments\":[{\"segmentId\":1,\"departureTime\":\"2022-01-24 09:45\",\"arrivalTime\":\"2022-01-24 13:05\",\"stop\":0,\"stops\":[],\"flightNumber\":\"1474\",\"bookingClass\":\"Q\",\"duration\":140,\"departureAirportCode\":\"FRA\",\"departureAirportTerminal\":\"1\",\"arrivalAirportCode\":\"KIV\",\"arrivalAirportTerminal\":\"\",\"operatingAirline\":\"CL\",\"opName\":\"LUFTHANSA CITYLINE GMBH\",\"airEquipType\":\"E90\",\"marketingAirline\":\"LH\",\"marriageGroup\":\"O\",\"mileage\":953,\"cabin\":\"Y\",\"meal\":\"\",\"fareCode\":\"Q03CLSE0\",\"baggage\":{\"ADT\":{\"carryOn\":true,\"allowPieces\":1},\"CHD\":{\"carryOn\":true,\"allowPieces\":1}},\"recheckBaggage\":false}],\"duration\":140}],\"maxSeats\":9,\"paxCnt\":3,\"validatingCarrier\":\"OS\",\"gds\":\"T\",\"pcc\":\"E9V\",\"cons\":\"GTT\",\"fareType\":\"PUB\",\"tripType\":\"MC\",\"cabin\":\"Y\",\"currency\":\"USD\",\"currencies\":[\"USD\"],\"currencyRates\":{\"USDUSD\":{\"from\":\"USD\",\"to\":\"USD\",\"rate\":1}},\"keys\":{\"travelport\":{\"traceId\":\"23d8e32d-b8eb-4578-9928-4674761747d6\",\"availabilitySources\":\"Q,Q,S,S\",\"type\":\"T\"},\"seatHoldSeg\":{\"trip\":0,\"segment\":0,\"seats\":9}},\"ngsFeatures\":{\"stars\":1,\"name\":\"BASIC\",\"list\":[]},\"meta\":{\"eip\":0,\"noavail\":false,\"searchId\":\"U0FMMTAxWTIxMDB8S0lWTE9OMjAyMi0wMS0xMnxMT05GUkEyMDIyLTAxLTE1fEZSQUtJVjIwMjItMDEtMjQ=\",\"lang\":\"en\",\"rank\":10,\"cheapest\":true,\"fastest\":true,\"best\":true,\"bags\":1,\"country\":\"us\",\"prod_types\":[\"PUB\"]},\"price\":493.8,\"originRate\":1,\"stops\":[1,0,0],\"time\":[{\"departure\":\"2022-01-12 16:00\",\"arrival\":\"2022-01-12 18:40\"},{\"departure\":\"2022-01-15 11:30\",\"arrival\":\"2022-01-15 14:05\"},{\"departure\":\"2022-01-24 09:45\",\"arrival\":\"2022-01-24 13:05\"}],\"bagFilter\":1,\"airportChange\":false,\"technicalStopCnt\":0,\"duration\":[280,95,140],\"totalDuration\":515,\"topCriteria\":\"fastestbestcheapest\",\"rank\":10}",
                    "fq_last_ticket_date": "2021-07-31",
                    "fq_json_booking": null,
                    "fq_ticket_json": null,
                    "booking_id": "O230850",
                    "fq_type_name": "Base",
                    "fq_fare_type_name": "Public",
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
                            "fqt_id": 526,
                            "fqt_uid": "fqt6103c94699a2e",
                            "fqt_key": null,
                            "duration": 280,
                            "segments": [
                                {
                                    "segmentId": 1,
                                    "fqs_uid": "fqs6103c9469c3c8",
                                    "departureTime": "2022-01-12 16:00",
                                    "arrivalTime": "2022-01-12 16:45",
                                    "fqs_stop": 0,
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
                                    "cabin": "Y",
                                    "meal": "",
                                    "fareCode": "K03CLSE8",
                                    "fqs_ticket_id": null,
                                    "fqs_recheck_baggage": 0,
                                    "mileage": 583,
                                    "departureLocation": "Chisinau",
                                    "arrivalLocation": "Vienna",
                                    "cabin": "Economy",
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
                                            "qss_departure_dt": "2021-09-09 00:00:00",
                                            "arrivalDateTime": "2021-09-08 00:00:00"
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
                                    "segmentId": 2,
                                    "fqs_uid": "fqs6103c9469e37b",
                                    "departureTime": "2022-01-12 17:15",
                                    "arrivalTime": "2022-01-12 18:40",
                                    "fqs_stop": 0,
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
                                    "cabin": "Y",
                                    "meal": "",
                                    "fareCode": "K03CLSE8",
                                    "fqs_ticket_id": null,
                                    "fqs_recheck_baggage": 0,
                                    "mileage": 774,
                                    "departureLocation": "Vienna",
                                    "arrivalLocation": "London",
                                    "cabin": "Economy",
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
                            "fqt_id": 527,
                            "fqt_uid": "fqt6103c9469f378",
                            "fqt_key": null,
                            "duration": 95,
                            "segments": [
                                {
                                    "segmentId": 1,
                                    "fqs_uid": "fqs6103c9469fa85",
                                    "departureTime": "2022-01-15 11:30",
                                    "arrivalTime": "2022-01-15 14:05",
                                    "fqs_stop": 0,
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
                                    "fqs_ticket_id": null,
                                    "fqs_recheck_baggage": 0,
                                    "mileage": 390,
                                    "departureLocation": "London",
                                    "arrivalLocation": "Frankfurt am Main",
                                    "cabin": "Economy",
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
                            "fqt_id": 528,
                            "fqt_uid": "fqt6103c946a08d6",
                            "fqt_key": null,
                            "duration": 140,
                            "segments": [
                                {
                                    "segmentId": 1,
                                    "fqs_uid": "fqs6103c946a0d33",
                                    "departureTime": "2022-01-24 09:45",
                                    "arrivalTime": "2022-01-24 13:05",
                                    "fqs_stop": 0,
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
                                    "cabin": "Y",
                                    "meal": "",
                                    "fareCode": "Q03CLSE0",
                                    "fqs_ticket_id": null,
                                    "fqs_recheck_baggage": 0,
                                    "mileage": 953,
                                    "departureLocation": "Frankfurt am Main",
                                    "arrivalLocation": "Chisinau",
                                    "cabin": "Economy",
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
                    "fq_gds": "C",
                    "fq_gds_pcc": "default",
                    "fq_gds_offer_id": null,
                    "fq_type_id": 0,
                    "fq_cabin_class": "E",
                    "fq_trip_type_id": 1,
                    "fq_main_airline": "RO",
                    "fq_fare_type_id": 1,
                    "fq_origin_search_data": "{\"key\":\"2_U0FMMTAxKlkyMDAwL0tJVkxPTjIwMjEtMDctMjkqUk9+I1JPMjAyI1JPMzkxfmxjOmVuX3Vz\",\"routingId\":2,\"prices\":{\"lastTicketDate\":\"2021-07-28 23:59\",\"totalPrice\":302.9,\"totalTax\":81.5,\"comm\":0,\"isCk\":true,\"CkAmount\":14.84,\"markupId\":0,\"markupUid\":\"\",\"markup\":14.84},\"passengers\":{\"ADT\":{\"codeAs\":\"ADT\",\"cnt\":2,\"baseFare\":110.7,\"pubBaseFare\":110.7,\"baseTax\":33.33,\"markup\":7.42,\"comm\":0,\"CkAmount\":7.42,\"price\":151.45,\"tax\":40.75,\"oBaseFare\":{\"amount\":92,\"currency\":\"EUR\"},\"oBaseTax\":{\"amount\":27.7,\"currency\":\"EUR\"},\"oCkAmount\":{\"amount\":6.17,\"currency\":\"EUR\"}}},\"trips\":[{\"tripId\":1,\"segments\":[{\"segmentId\":1,\"departureTime\":\"2021-07-29 09:30\",\"arrivalTime\":\"2021-07-29 10:45\",\"stop\":0,\"stops\":null,\"flightNumber\":\"202\",\"bookingClass\":\"E\",\"duration\":75,\"departureAirportCode\":\"KIV\",\"departureAirportTerminal\":\"\",\"arrivalAirportCode\":\"OTP\",\"arrivalAirportTerminal\":\"\",\"operatingAirline\":\"RO\",\"airEquipType\":\"AT7\",\"marketingAirline\":\"RO\",\"marriageGroup\":\"\",\"cabin\":\"Y\",\"meal\":\"\",\"fareCode\":\"EOWSVRMD\",\"baggage\":{\"ADT\":{\"carryOn\":true,\"allowPieces\":1}},\"recheckBaggage\":false},{\"segmentId\":2,\"departureTime\":\"2021-07-29 12:20\",\"arrivalTime\":\"2021-07-29 14:05\",\"stop\":0,\"stops\":null,\"flightNumber\":\"391\",\"bookingClass\":\"E\",\"duration\":225,\"departureAirportCode\":\"OTP\",\"departureAirportTerminal\":\"\",\"arrivalAirportCode\":\"LHR\",\"arrivalAirportTerminal\":\"\",\"operatingAirline\":\"RO\",\"airEquipType\":\"318\",\"marketingAirline\":\"RO\",\"marriageGroup\":\"\",\"cabin\":\"Y\",\"meal\":\"\",\"fareCode\":\"EOWSVRGB\",\"baggage\":{\"ADT\":{\"carryOn\":true,\"allowPieces\":1}},\"recheckBaggage\":false}],\"duration\":395}],\"maxSeats\":3,\"paxCnt\":2,\"validatingCarrier\":\"RO\",\"gds\":\"C\",\"pcc\":\"default\",\"cons\":\"AER\",\"fareType\":\"PUB\",\"tripType\":\"OW\",\"cabin\":\"Y\",\"currency\":\"USD\",\"currencies\":[\"USD\",\"EUR\"],\"currencyRates\":{\"EURUSD\":{\"from\":\"EUR\",\"to\":\"USD\",\"rate\":1.20328},\"USDUSD\":{\"from\":\"USD\",\"to\":\"USD\",\"rate\":1}},\"keys\":{\"cockpit\":{\"itineraryIds\":[\"D3537439481d_ROUNDTRIP_0_0_0_0\"],\"fareIds\":[\"D3537439481d_ROUNDTRIP_0\"],\"webServiceLogId\":\"EM483101d9441a09d\",\"sessionId\":\"3af91858-e306-4b40-83af-108c593f2a36\",\"type\":\"C\"}},\"ngsFeatures\":{\"stars\":1,\"name\":\"BASIC\",\"list\":[]},\"meta\":{\"eip\":0,\"noavail\":false,\"searchId\":\"U0FMMTAxWTIwMDB8S0lWTE9OMjAyMS0wNy0yOQ==\",\"lang\":\"en\",\"rank\":8.987654,\"cheapest\":false,\"fastest\":false,\"best\":false,\"bags\":1,\"country\":\"us\",\"prod_types\":[\"PUB\"]},\"price\":151.45,\"originRate\":1,\"stops\":[1],\"time\":[{\"departure\":\"2021-07-29 09:30\",\"arrival\":\"2021-07-29 14:05\"}],\"bagFilter\":1,\"airportChange\":false,\"technicalStopCnt\":0,\"duration\":[395],\"totalDuration\":395,\"topCriteria\":\"\",\"rank\":8.987654}",
                    "fq_last_ticket_date": "2021-07-28",
                    "fq_json_booking": null,
                    "fq_ticket_json": null,
                    "booking_id": "O230851",
                    "fq_type_name": "Base",
                    "fq_fare_type_name": "Public",
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
                            "fqt_id": 525,
                            "fqt_uid": "fqt61015f35534ec",
                            "fqt_key": null,
                            "duration": 395,
                            "segments": [
                                {
                                    "segmentId": 1,
                                    "fqs_uid": "fqs61015f3554892",
                                    "departureTime": "2021-07-29 09:30",
                                    "arrivalTime": "2021-07-29 10:45",
                                    "fqs_stop": 0,
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
                                    "cabin": "Y",
                                    "meal": "",
                                    "fareCode": "EOWSVRMD",
                                    "fqs_ticket_id": null,
                                    "fqs_recheck_baggage": 0,
                                    "mileage": null,
                                    "departureLocation": "Chisinau",
                                    "arrivalLocation": "Bucharest",
                                    "cabin": "Economy",
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
                                    "segmentId": 2,
                                    "fqs_uid": "fqs61015f35565ef",
                                    "departureTime": "2021-07-29 12:20",
                                    "arrivalTime": "2021-07-29 14:05",
                                    "fqs_stop": 0,
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
                                    "cabin": "Y",
                                    "meal": "",
                                    "fareCode": "EOWSVRGB",
                                    "fqs_ticket_id": null,
                                    "fqs_recheck_baggage": 0,
                                    "mileage": null,
                                    "departureLocation": "Bucharest",
                                    "arrivalLocation": "London",
                                    "cabin": "Economy",
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
     * @apiParam {string{32}}       [reprotection_quote_gid] Re-protection Product Quote GID (required for type = "confirm", "modify")
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
     */
    public function actionReprotectionDecision()
    {
        $post = Yii::$app->request->post();
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
            if ($form->isConfirm()) {
                Yii::createObject(reprotectionDecision\confirm\Confirm::class)->handle($form->reprotection_quote_gid);
            } elseif ($form->isModify()) {
                Yii::createObject(reprotectionDecision\modify\Modify::class)->handle($form->reprotection_quote_gid, $form->flight_product_quote);
            } elseif ($form->isRefund()) {
                Yii::createObject(reprotectionDecision\refund\Refund::class)->handle($form->reprotection_quote_gid);
            } else {
                throw new \DomainException('Undefined type');
            }

            return new SuccessResponse(
                new DataMessage([
                    'success' => true,
                ])
            );
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Reprotection decision error',
                'request' => $form->getAttributes(),
                'error' => $e->getMessage(),
                'exception' => AppHelper::throwableLog($e, true),
            ], 'FlightController:reprotectionDecision');
            return new ErrorResponse(
                new DataMessage([
                    'success' => false,
                    'error' => $e->getMessage(),
                ])
            );
        }
    }
}

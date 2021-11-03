<?php

namespace webapi\modules\v2\controllers;

use common\components\jobs\VoluntaryExchangeCreateJob;
use modules\flight\models\FlightRequest;
use modules\flight\src\useCases\voluntaryExchange\service\BoRequestVoluntaryExchangeService;
use modules\flight\src\useCases\voluntaryExchange\service\CaseVoluntaryExchangeService as CaseService;
use modules\flight\src\useCases\voluntaryExchange\service\CleanDataVoluntaryExchangeService;
use modules\flight\src\useCases\voluntaryExchange\service\VoluntaryExchangeObjectCollection;
use modules\flight\src\useCases\voluntaryExchangeConfirm\form\VoluntaryExchangeConfirmForm;
use modules\flight\src\useCases\voluntaryExchangeCreate\form\VoluntaryExchangeCreateForm;
use modules\flight\src\useCases\voluntaryExchangeCreate\service\VoluntaryExchangeCreateHandler;
use modules\flight\src\useCases\voluntaryExchangeCreate\service\VoluntaryExchangeCreateService;
use modules\flight\src\useCases\voluntaryExchangeInfo\form\VoluntaryExchangeInfoForm;
use modules\flight\src\useCases\voluntaryExchangeInfo\service\VoluntaryExchangeInfoService;
use modules\product\src\entities\productQuote\ProductQuoteQuery;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use sales\entities\cases\CaseEventLog;
use sales\helpers\app\AppHelper;
use sales\helpers\app\HttpStatusCodeHelper;
use sales\helpers\setting\SettingHelper;
use webapi\src\ApiCodeException;
use webapi\src\logger\ApiLogger;
use webapi\src\logger\behaviors\filters\creditCard\CreditCardFilter;
use webapi\src\logger\behaviors\SimpleLoggerBehavior;
use webapi\src\Messages;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\DataMessage;
use webapi\src\response\messages\ErrorName;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\messages\TypeMessage;
use webapi\src\response\SuccessResponse;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class FlightQuoteExchangeController
 *
 * @property VoluntaryExchangeObjectCollection $objectCollection
 * @property BoRequestVoluntaryExchangeService $boRequestVoluntaryExchangeService
 */
class FlightQuoteExchangeController extends BaseController
{
    private VoluntaryExchangeObjectCollection $objectCollection;
    private BoRequestVoluntaryExchangeService $boRequestVoluntaryExchangeService;

    /**
     * @param $id
     * @param $module
     * @param ApiLogger $logger
     * @param VoluntaryExchangeObjectCollection $voluntaryExchangeObjectCollection
     * @param BoRequestVoluntaryExchangeService $boRequestVoluntaryExchangeService
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        ApiLogger $logger,
        VoluntaryExchangeObjectCollection $voluntaryExchangeObjectCollection,
        BoRequestVoluntaryExchangeService $boRequestVoluntaryExchangeService,
        $config = []
    ) {
        $this->objectCollection = $voluntaryExchangeObjectCollection;
        $this->boRequestVoluntaryExchangeService = $boRequestVoluntaryExchangeService;
        parent::__construct($id, $module, $logger, $config);
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['logger'] = [
            'class' => SimpleLoggerBehavior::class,
            'filter' => CreditCardFilter::class,
            'except' => [],
        ];
        return $behaviors;
    }

    /**
     * @api {post} /v2/flight-quote-exchange/create Flight Voluntary Exchange Create
     * @apiVersion 0.2.0
     * @apiName Flight Voluntary Exchange Create
     * @apiGroup Flight Voluntary Exchange
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{7..10}}                bookingId                    Booking ID
     * @apiParam {string{150}}                  apiKey                       ApiKey (Project API Key)
     * @apiParam {object}                       exchange                     Exchange Data Info
     * @apiParam {object}                       exchange.prices                       Prices
     * @apiParam {number}                       exchange.prices.totalPrice            Total Price (total for exchange pay)
     * @apiParam {number}                       exchange.prices.comm                  Comm
     * @apiParam {bool}                         exchange.prices.isCk                  isCk
     * @apiParam {object}                       exchange.tickets                      Tickets
     * @apiParam {string}                       exchange.tickets.numRef               NumRef
     * @apiParam {string}                       exchange.tickets.firstName            FirstName
     * @apiParam {string}                       exchange.tickets.lastName             LastName
     * @apiParam {string{3}}                    exchange.tickets.paxType              paxType
     * @apiParam {string}                       exchange.tickets.number               Number
     * @apiParam {object}                       [exchange.passengers]                 Passengers
     * @apiParam {string{3}}                    exchange.passengers.ADT               Pax Type (ADT,CHD,INF)
     * @apiParam {string{3}}                    exchange.passengers.ADT.codeAs        Pax Type Code
     * @apiParam {int}                          exchange.passengers.ADT.cnt           Cnt
     * @apiParam {number}                       exchange.passengers.ADT.baseFare      Base Fare (diffFare)
     * @apiParam {number}                       exchange.passengers.ADT.pubBaseFare   Pub Base Fare
     * @apiParam {number}                       exchange.passengers.ADT.baseTax       Base Tax (airlinePenalty)
     * @apiParam {number}                       exchange.passengers.ADT.markup        Markup (processingFee)
     * @apiParam {number}                       exchange.passengers.ADT.comm          Comm
     * @apiParam {number}                       exchange.passengers.ADT.price         Price (total for exchange pay)
     * @apiParam {number}                       exchange.passengers.ADT.tax           Tax
     * @apiParam {object}                       [exchange.passengers.ADT.oBaseFare]                  oBaseFare
     * @apiParam {number}                       exchange.passengers.ADT.oBaseFare.amount             oBaseFare Amount
     * @apiParam {string{3}}                    exchange.passengers.ADT.oBaseFare.currency           oBaseFare Currency
     * @apiParam {object}                       [exchange.passengers.ADT.oBaseTax]                   oBaseTax
     * @apiParam {number}                       exchange.passengers.ADT.oBaseTax.amount              oBaseTax Amount
     * @apiParam {string{3}}                    exchange.passengers.ADT.oBaseTax.currency            oBaseTax Currency
     * @apiParam {object}                       [exchange.passengers.ADT.oExchangeFareDiff]          oExchangeFareDiff
     * @apiParam {number}                       exchange.passengers.ADT.oExchangeFareDiff.amount     oExchangeFareDiff Amount
     * @apiParam {string{3}}                    exchange.passengers.ADT.oExchangeFareDiff.currency   oExchangeFareDiff Currency
     * @apiParam {object}                       exchange.passengers.ADT.oExchangeTaxDiff             oExchangeTaxDiff
     * @apiParam {number}                       exchange.passengers.ADT.oBaseFare.amount             oExchangeTaxDiff Amount
     * @apiParam {string{3}}                    exchange.passengers.ADT.oBaseFare.currency           oExchangeTaxDiff Currency
     * @apiParam {object[]}                     exchange.trips                                        Trips
     * @apiParam {int}                          exchange.trips.tripId                                 Trip Id
     * @apiParam {object[]}                     exchange.trips.segments                               Segments
     * @apiParam {int}                          exchange.trips.segments.segmentId                     Segment Id
     * @apiParam {string{format Y-m-d H:i}}     exchange.trips.segments.departureTime                 DepartureTime
     * @apiParam {string{format Y-m-d H:i}}     exchange.trips.segments.arrivalTime                   ArrivalTime
     * @apiParam {int}                          [exchange.trips.segments.stop]                        Stop
     * @apiParam {object[]}                     [exchange.trips.segments.stops]                       Stops
     * @apiParam {string{3}}                    exchange.trips.segments.stops.locationCode            Location Code
     * @apiParam {string{format Y-m-d H:i}}     exchange.trips.segments.stops.departureDateTime       Departure DateTime
     * @apiParam {string{format Y-m-d H:i}}     exchange.trips.segments.stops.arrivalDateTime         Departure DateTime
     * @apiParam {int}                          exchange.trips.segments.stops.duration                Duration
     * @apiParam {int}                          exchange.trips.segments.stops.elapsedTime             Elapsed Time
     * @apiParam {int}                          exchange.trips.segments.stops.equipment               Equipment
     * @apiParam {string{3}}                    exchange.trips.segments.departureAirportCode     Departure Airport Code IATA
     * @apiParam {string{3}}                    exchange.trips.segments.arrivalAirportCode       Arrival Airport Code IATA
     * @apiParam {string{5}}}                   exchange.trips.segments.flightNumber             Flight Number
     * @apiParam {string{1}}                    exchange.trips.segments.bookingClass             BookingClass
     * @apiParam {int}                          exchange.trips.segments.duration                 Segment duration
     * @apiParam {string{3}}                    exchange.trips.segments.departureAirportTerminal]     Departure Airport Terminal Code
     * @apiParam {string{3}}                    [exchange.trips.segments.arrivalAirportTerminal]       Arrival Airport Terminal Code
     * @apiParam {string{2}}                    [exchange.trips.segments.operatingAirline]       Operating Airline
     * @apiParam {string{2}}                    [exchange.trips.segments.marketingAirline]       Marketing Airline
     * @apiParam {string{30}}                   [exchange.trips.segments.airEquipType]           AirEquipType
     * @apiParam {string{3}}                    [exchange.trips.segments.marriageGroup]          MarriageGroup
     * @apiParam {int}                          [exchange.trips.segments.mileage]                Mileage
     * @apiParam {string{2}}                    [exchange.trips.segments.meal]                   Meal
     * @apiParam {string{50}}                   [exchange.trips.segments.fareCode]               Fare Code
     * @apiParam {bool}                         [exchange.trips.segments.recheckBaggage]         Recheck Baggage
     * @apiParam {int}                          exchange.paxCnt                                  Pax Cnt
     * @apiParam {string{2}}                    exchange.validatingCarrier                       ValidatingCarrier
     * @apiParam {string{2}}                    exchange.gds                                     Gds
     * @apiParam {string{10}}                   exchange.pcc                                     pcc
     * @apiParam {string{50}}                   exchange.fareType                                Fare Type
     * @apiParam {string{1}}                    exchange.cabin                                   Cabin
     * @apiParam {string{3}}                    exchange.cons                                     Consolidator
     * @apiParam {string{3}}                    exchange.currency                                Currency
     * @apiParam {array[]}                      [exchange.currencies]                              Currencies (For example [USD])
     * @apiParam {object[]}                     [exchange.currencyRates]                         CurrencyRates
     * @apiParam {string{6}}                    exchange.currencyRates.USDUSD                    Currency Codes
     * @apiParam {string{3}}                    exchange.currencyRates.USDUSD.from               Currency Code
     * @apiParam {string{3}}                    exchange.currencyRates.USDUSD.to                 Currency Code
     * @apiParam {number}                       exchange.currencyRates.USDUSD.rate               Rate
     * @apiParam {object}                       [exchange.keys]                                  Keys
     * @apiParam {object}                       [exchange.meta]                                  Meta
     * @apiParam {object}                       [billing]                    Billing
     * @apiParam {string{30}}                   billing.first_name           First name
     * @apiParam {string{30}}                   billing.last_name            Last name
     * @apiParam {string{30}}                   [billing.middle_name]        Middle name
     * @apiParam {string{40}}                   [billing.company_name]       Company
     * @apiParam {string{50}}                   billing.address_line1        Address line 1
     * @apiParam {string{50}}                   [billing.address_line2]      Address line 2
     * @apiParam {string{30}}                   billing.city                 City
     * @apiParam {string{40}}                   [billing.state]              State
     * @apiParam {string{2}}                    billing.country_id           Country code (for example "US")
     * @apiParam {string}                       billing.country              Country name
     * @apiParam {string{10}}                   [billing.zip]                Zip
     * @apiParam {string{20}}                   [billing.contact_phone]      Contact phone
     * @apiParam {string{160}}                  [billing.contact_email]      Contact email
     * @apiParam {string{60}}                   [billing.contact_name]       Contact name
     * @apiParam {object}                       [payment_request]                                   Payment request
     * @apiParam {number}                       payment_request.amount                              Customer must pay for initiate refund process
     * @apiParam {string{3}}                    payment_request.currency                            Currency code
     * @apiParam {string{2}}                    payment_request.method_key                          Method key (for example "cc")
     * @apiParam {object}                       payment_request.method_data                         Method data
     * @apiParam {object}                       payment_request.method_data.card                    Card (for credit card)
     * @apiParam {string{..20}}                 payment_request.method_data.card.number             Number
     * @apiParam {string{..50}}                 [payment_request.method_data.card.holder_name]      Holder name
     * @apiParam {int}                          payment_request.method_data.card.expiration_month   Month
     * @apiParam {int}                          payment_request.method_data.card.expiration_year    Year
     * @apiParam {string{..4}}                  payment_request.method_data.card.cvv                CVV
     *
     * @apiParamExample {json} Request-Example:
         {
            "bookingId": "XXXYYYZ",
            "apiKey": "test-api-key",
            "exchange": {
                "trips": [
                    {
                        "tripId": 1,
                        "segments": [
                            {
                                "segmentId": 1,
                                "departureTime": "2022-01-10 20:15",
                                "arrivalTime": "2022-01-11 21:10",
                                "stop": 0,
                                "stops": [
                                    {
                                        "locationCode": "LFW",
                                        "departureDateTime": "2022-01-11 12:35",
                                        "arrivalDateTime": "2022-01-11 11:35",
                                        "duration": 60,
                                        "elapsedTime": 620,
                                        "equipment": "787"
                                    }
                                ],
                                "flightNumber": "513",
                                "bookingClass": "H",
                                "duration": 1015,
                                "departureAirportCode": "JFK",
                                "departureAirportTerminal": "8",
                                "arrivalAirportCode": "ADD",
                                "arrivalAirportTerminal": "2",
                                "operatingAirline": "ET",
                                "airEquipType": "787",
                                "marketingAirline": "ET",
                                "marriageGroup": "O",
                                "cabin": "Y",
                                "meal": "DL",
                                "fareCode": "HLESUS",
                                "recheckBaggage": false
                            },
                            {
                                "segmentId": 2,
                                "departureTime": "2022-01-11 23:15",
                                "arrivalTime": "2022-01-12 01:20",
                                "stop": 0,
                                "stops": null,
                                "flightNumber": "308",
                                "bookingClass": "H",
                                "duration": 125,
                                "departureAirportCode": "ADD",
                                "departureAirportTerminal": "2",
                                "arrivalAirportCode": "NBO",
                                "arrivalAirportTerminal": "1C",
                                "operatingAirline": "ET",
                                "airEquipType": "738",
                                "marketingAirline": "ET",
                                "marriageGroup": "I",
                                "cabin": "Y",
                                "meal": "D",
                                "fareCode": "HLESUS",
                                "recheckBaggage": false
                            }
                        ],
                        "duration": 1265
                    }
                ],
                "tickets": [
                    {
                        "numRef": "1.1",
                        "firstName": "PAULA ANNE",
                        "lastName": "ALVAREZ",
                        "paxType": "ADT",
                        "number": "123456789"
                    },
                    {
                        "numRef": "2.1",
                        "firstName": "ANNE",
                        "lastName": "ALVAREZ",
                        "paxType": "ADT",
                        "number": "987654321"
                    }
                ],
                "passengers": {
                    "ADT": {
                        "codeAs": "JCB",
                        "cnt": 1,
                        "baseFare": 32.12,
                        "pubBaseFare": 32.12,
                        "baseTax": 300,
                        "markup": 0,
                        "comm": 0,
                        "price": 332.12,
                        "tax": 300,
                        "oBaseFare": {
                            "amount": 32.120003,
                            "currency": "USD"
                        },
                        "oBaseTax": {
                            "amount": 300,
                            "currency": "USD"
                        },
                        "oExchangeFareDiff": {
                            "amount": 8,
                            "currency": "USD"
                        },
                        "oExchangeTaxDiff": {
                            "amount": 24.12,
                            "currency": "USD"
                        }
                    }
                },
                "validatingCarrier": "AA",
                "gds": "S",
                "pcc": "G9MJ",
                "cons": "GTT",
                "fareType": "SR",
                "cabin": "Y",
                "currency": "USD",
                "currencies": [
                    "USD"
                ],
                "currencyRates": {
                    "USDUSD": {
                        "from": "USD",
                        "to": "USD",
                        "rate": 1
                    }
                },
                "keys": {},
                "meta": {}
            },
            "billing": {
                  "first_name": "John",
                  "last_name": "Doe",
                  "middle_name": "",
                  "address_line1": "1013 Weda Cir",
                  "address_line2": "",
                  "country_id": "US",
                  "country" : "United States",
                  "city": "Mayfield",
                  "state": "KY",
                  "zip": "99999",
                  "company_name": "",
                  "contact_phone": "+19074861000",
                  "contact_email": "test@test.com",
                  "contact_name": "Test Name"
            },
            "payment_request": {
                  "method_key": "card",
                  "currency": "USD",
                  "method_data": {
                      "card": {
                          "number": "4111555577778888",
                          "holder_name": "Test test",
                          "expiration_month": 10,
                          "expiration_year": 23,
                          "cvv": "123"
                      }
                  },
                  "amount": 112.25
            }
        }
     *
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {
     *        "status": 200,
     *        "message": "OK",
     *        "data": {
                    "resultMessage": "Processing was successful",
                    "originQuoteGid" : "a1275b33cda3bbcbeea2d684475a7e8a",
                    "changeQuoteGid" : "5c63db4e9d4d24f480088fd5e194e4f5",
                    "productQuoteChangeGid" : "ee61d0abb62d96879e2c29ddde403650",
                    "caseGid" : "e7dce13b4e6a5f3ccc2cec9c21fa3255"
               },
     *        "code": "13200",
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
     *        "message": "Load data error",
     *        "errors": [
     *           "Not found data on POST request"
     *        ],
     *        "code": "13106",
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
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     *
     * @apiErrorExample {json} Error-Response (Unprocessable entity):
     * HTTP/1.1 422 Unprocessable entity
     * {
     *        "status": 422,
     *        "message": "Validation error",
     *        "errors": [
     *            "bookingId": [
     *               "bookingId cannot be blank."
     *             ]
     *        ],
     *        "code": "13107",
     *        "technical": {
     *           ...
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     *
     * @apiErrorExample {json} Error-Response (Unprocessable entity):
     * HTTP/1.1 422 Unprocessable entity
     * {
     *      "status": 422,
     *      "message": "Error",
     *      "errors": [
     *          "FlightRequest (hash: df578e1ac5bc11b34eb7eaea8714c5e4) already processed"
     *      ],
     *      "code": "13113",
     *      "technical": {
     *         ...
     *      },
     *      "request": {
     *         ...
     *      }
     * }
     *
     * @apiErrorExample {json} Error-Response (Unprocessable entity):
     * HTTP/1.1 422 Unprocessable entity
     * {
     *      "status": 422,
     *      "message": "Error",
     *      "errors": [
     *          "Quote not available for exchange"
     *      ],
     *      "code": "13113",
     *      "technical": {
     *         ...
     *      },
     *      "request": {
     *         ...
     *      }
     * }
     *
     * @apiErrorExample {json} Error-Response (Unprocessable entity):
     * HTTP/1.1 422 Unprocessable entity
     * {
     *      "status": 422,
     *      "message": "Error",
     *      "errors": [
     *          "Case saving error"
     *      ],
     *      "code": "21101",
     *      "technical": {
     *         ...
     *      },
     *      "request": {
     *         ...
     *      }
     * }
     *
     * @apiErrorExample {json} Error-Response (Internal Server Error):
     * HTTP/1.1 500 Internal Server Error
     * {
     *      "status": 500,
     *      "message": "Error",
     *      "errors": [
     *          "Server Error"
     *      ],
     *      "code": 0,
     *      "technical": {
     *         ...
     *      },
     *      "request": {
     *         ...
     *      }
     * }
     */
    public function actionCreate()
    {
        try {
            $post = Yii::$app->request->post();
        } catch (\Throwable $throwable) {
            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::BAD_REQUEST),
                new MessageMessage(Messages::POST_DATA_ERROR),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage(ApiCodeException::POST_DATA_NOT_LOADED)
            );
        }

        if (!$project = $this->auth->auProject) {
            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::BAD_REQUEST),
                new ErrorsMessage('Not found Project with current user: ' . $this->auth->au_api_username),
                new CodeMessage(ApiCodeException::NOT_FOUND_PROJECT_CURRENT_USER)
            );
        }

        $voluntaryExchangeCreateForm = new VoluntaryExchangeCreateForm();
        if (!$voluntaryExchangeCreateForm->load($post)) {
            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::BAD_REQUEST),
                new ErrorsMessage(Messages::LOAD_DATA_ERROR),
                new CodeMessage(ApiCodeException::POST_DATA_NOT_LOADED)
            );
        }

        if (!$voluntaryExchangeCreateForm->validate()) {
            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY),
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($voluntaryExchangeCreateForm->getErrors()),
                new CodeMessage(ApiCodeException::FAILED_FORM_VALIDATE)
            );
        }

        try {
            if ($productQuote = ProductQuoteQuery::getProductQuoteByBookingId($voluntaryExchangeCreateForm->bookingId)) {
                if ($productQuote->isChangeable()) {
                    if ($productQuote->productQuoteRefundsActive || $productQuote->productQuoteChangesActive) {
                        throw new \DomainException('Product Quote not available for exchange');
                    }
                } else {
                    throw new \DomainException('Product Quote not available for exchange. Status(' .
                        ProductQuoteStatus::getName($productQuote->pq_status_id) . ')');
                }
            }

            $flightRequest = FlightRequest::create(
                $voluntaryExchangeCreateForm->bookingId,
                FlightRequest::TYPE_VOLUNTARY_EXCHANGE_CREATE,
                $post,
                $project->id,
                $this->auth->getId()
            );
            $flightRequest = $this->objectCollection->getFlightRequestRepository()->save($flightRequest);

            if (!$case = CaseService::getLastActiveCaseByBookingId($flightRequest->fr_booking_id)) {
                $case = CaseService::createCase(
                    $flightRequest->fr_booking_id,
                    $flightRequest->fr_project_id,
                    true,
                    $this->objectCollection
                );
            }

            $voluntaryExchangeCreateHandler = new VoluntaryExchangeCreateHandler($case, $flightRequest, $this->objectCollection);
            try {
                if (!$responseBo = $this->boRequestVoluntaryExchangeService->sendVoluntaryExchange($post, $voluntaryExchangeCreateForm)) {
                    $case->addEventLog(
                        CaseEventLog::VOLUNTARY_EXCHANGE_CREATE,
                        'Request (create Voluntary Exchange) to Back Office is failed'
                    );
                    throw new \RuntimeException('Request to Back Office is failed', ApiCodeException::REQUEST_TO_BACK_OFFICE_ERROR);
                }

                $responseBoStatus = ($responseBo['status'] === 'Success');
                $voluntaryExchangeCreateHandler->processing($responseBoStatus);

                $dataJson = $flightRequest->fr_data_json;
                $dataJson['responseBo'] = $responseBo;
                $flightRequest->fr_data_json = $dataJson;
                $this->objectCollection->getFlightRequestRepository()->save($flightRequest);

                if ($responseBoStatus) {
                    $case->addEventLog(
                        CaseEventLog::VOLUNTARY_EXCHANGE_CREATE,
                        'Request (create Voluntary Exchange) to Back Office is success',
                        $responseBo
                    );
                } else {
                    $case->addEventLog(
                        CaseEventLog::VOLUNTARY_EXCHANGE_CREATE,
                        'Request (create Voluntary Exchange) to Back Office is error',
                        $responseBo
                    );
                    throw new \RuntimeException('Request to Back Office is failed.' . $responseBo['message'] ?? '', ApiCodeException::REQUEST_TO_BACK_OFFICE_ERROR);
                }
            } catch (\Throwable $throwable) {
                $voluntaryExchangeCreateHandler->failProcess($throwable->getMessage());
                throw $throwable;
            }

            $voluntaryExchangeCreateHandler->doneProcess();

            $dataMessage['resultMessage'] = 'Processing was successful';
            $dataMessage['originQuoteGid'] = $voluntaryExchangeCreateHandler->getOriginProductQuote()->pq_gid;
            $dataMessage['changeQuoteGid'] = $voluntaryExchangeCreateHandler->getVoluntaryExchangeQuote()->pq_gid;
            $dataMessage['productQuoteChangeGid'] = $voluntaryExchangeCreateHandler->getProductQuoteChange()->pqc_gid;
            $dataMessage['caseGid'] = $case->cs_gid;

            return new SuccessResponse(
                new DataMessage($dataMessage),
                new CodeMessage(ApiCodeException::SUCCESS)
            );
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['bookingId'] = $post['bookingId'] ?? null;
            $message['apiUser'] = [
                'username' => $this->auth->au_api_username ?? null,
                'project' => $this->auth->auProject->project_key ?? null,
            ];
            \Yii::warning($message, 'FlightQuoteExchangeController:actionInfo:Warning');

            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage($throwable->getCode())
            );
        } catch (\Throwable $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['bookingId'] = $post['bookingId'] ?? null;
            $message['apiUser'] = [
                'username' => $this->auth->au_api_username ?? null,
                'project' => $this->auth->auProject->project_key ?? null,
            ];
            \Yii::error($message, 'FlightQuoteExchangeController:actionInfo:Throwable');

            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::INTERNAL_SERVER_ERROR),
                new ErrorsMessage('Server error. Please try again later.'),
                new CodeMessage($throwable->getCode())
            );
        }
    }

    /**
     * @api {post} /v2/flight-quote-exchange/confirm Flight Voluntary Exchange Confirm
     * @apiVersion 0.2.0
     * @apiName Flight Voluntary Exchange Confirm
     * @apiGroup Flight Voluntary Exchange
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{7..10}}        booking_id                   Booking ID
     * @apiParam {string{32}}           quote_gid                    Product Quote GID
     * @apiParam {object}               billing                      Billing
     * @apiParam {string{30}}           billing.first_name           First name
     * @apiParam {string{30}}           billing.last_name            Last name
     * @apiParam {string{30}}           [billing.middle_name]        Middle name
     * @apiParam {string{40}}           [billing.company_name]       Company
     * @apiParam {string{50}}           billing.address_line1        Address line 1
     * @apiParam {string{50}}           [billing.address_line2]      Address line 2
     * @apiParam {string{30}}           billing.city                 City
     * @apiParam {string{40}}           [billing.state]              State
     * @apiParam {string{2}}            billing.country_id           Country code (for example "US")
     * @apiParam {string{10}}           [billing.zip]                Zip
     * @apiParam {string{20}}           [billing.contact_phone]      Contact phone
     * @apiParam {string{160}}          [billing.contact_email]      Contact email
     * @apiParam {string{60}}           [billing.contact_name]       Contact name
     * @apiParam {object}               [payment_request]                    Payment request
     * @apiParam {number}               payment_request.amount               Amount
     * @apiParam {string{3}}            payment_request.currency             Currency code
     * @apiParam {string{2}}            payment_request.method_key           Method key (for example "cc")
     * @apiParam {object}               payment_request.method_data          Method data
     * @apiParam {object}               payment_request.method_data.card     Card (for credit card)
     * @apiParam {string{50}}           payment_request.method_data.card.number          Number
     * @apiParam {string{50}}           [payment_request.method_data.card.holder_name]   Holder name
     * @apiParam {int}                  payment_request.method_data.card.expiration_month       Month
     * @apiParam {int}                  payment_request.method_data.card.expiration_year        Year
     * @apiParam {string{32}}           payment_request.method_data.card.cvv             CVV
     *
     * @apiParamExample {json} Request-Example:
         {
            "booking_id":"XXXYYYZ",
            "quote_gid": "2f2887a061f8069f7ada8af9e062f0f4",
            "payment":{
                "method_key":"cc",
                "method_data":{
                    "card":{
                        "number":"4111555577778888",
                        "holder_name":"John Doe",
                        "expiration_month":10,
                        "expiration_year":2022,
                        "cvv":"097"
                    }
                },
                "amount":29.95,
                "currency":"USD"
            },
            "billing":{
                "first_name":"John",
                "last_name":"Doe",
                "middle_name":null,
                "company_name":"General Motors",
                "address_line1":"123 Main Street",
                "address_line2":"",
                "city":"Paris",
                "state":"State",
                "country":"United States",
                "zip":"94000",
                "contact_phone":"+137396512345",
                "contact_email":"alex@test.com",
                "contact_name":"Mr. Alexander"
            }
        }
     *
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {
     *        "status": 200,
     *        "message": "OK",
     *        "data": {
                    "todo": "TODO::"
               },
     *        "code": "13200",
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
     *        "code": "13106",
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
     *            "booking_id": [
     *               "booking_id cannot be blank."
     *             ]
     *        ],
     *        "code": "13107",
     *        "technical": {
     *           ...
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     */
    public function actionConfirm()
    {
        try {
            $post = Yii::$app->request->post();
        } catch (\Throwable $throwable) {
            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::BAD_REQUEST),
                new MessageMessage(Messages::POST_DATA_ERROR),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage(ApiCodeException::POST_DATA_NOT_LOADED)
            );
        }

        if (!$project = $this->auth->auProject) {
            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::BAD_REQUEST),
                new ErrorsMessage('Not found Project with current user: ' . $this->auth->au_api_username),
                new CodeMessage(ApiCodeException::NOT_FOUND_PROJECT_CURRENT_USER)
            );
        }

        $voluntaryExchangeConfirmForm = new VoluntaryExchangeConfirmForm();
        if (!$voluntaryExchangeConfirmForm->load($post)) {
            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::BAD_REQUEST),
                new ErrorsMessage(Messages::LOAD_DATA_ERROR),
                new CodeMessage(ApiCodeException::POST_DATA_NOT_LOADED)
            );
        }
        if (!$voluntaryExchangeConfirmForm->validate()) {
            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY),
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($voluntaryExchangeConfirmForm->getErrors()),
                new CodeMessage(ApiCodeException::FAILED_FORM_VALIDATE)
            );
        }

        try {
            $bookingId = $voluntaryExchangeConfirmForm->booking_id;
            if ($productQuoteChange = VoluntaryExchangeInfoService::getLastProductQuoteChange($bookingId)) {
                throw new \RuntimeException('VoluntaryExchange by BookingID(' . $bookingId . ') already processed');
            }

            /* TODO::
                add request to BO - https://dev-backoffice.travel-dev.com/docs/api/#api-AirOrder_Self-Service-Create_Exchange_Order
             */

            $dataMessage['resultMessage'] = 'TODO::';

            return new SuccessResponse(
                new DataMessage($dataMessage),
                new CodeMessage(ApiCodeException::SUCCESS)
            );
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['post'] = $post;
            $message['apiUser'] = [
                'username' => $this->auth->au_api_username ?? null,
                'project' => $this->auth->auProject->project_key ?? null,
            ];
            \Yii::warning($message, 'FlightQuoteExchangeController:actionInfo:Warning');

            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage($throwable->getCode())
            );
        } catch (\Throwable $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['post'] = $post;
            $message['apiUser'] = [
                'username' => $this->auth->au_api_username ?? null,
                'project' => $this->auth->auProject->project_key ?? null,
            ];
            \Yii::error($message, 'FlightQuoteExchangeController:actionInfo:Throwable');

            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::INTERNAL_SERVER_ERROR),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage($throwable->getCode())
            );
        }
    }

    /**
     * @api {post} /v2/flight-quote-exchange/info Flight Voluntary Exchange Info
     * @apiVersion 0.2.0
     * @apiName Flight Voluntary Exchange Info
     * @apiGroup Flight Voluntary Exchange
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
     *
     * @apiParamExample {json} Request-Example:
     *  {
     *      "booking_id": "XXXYYYZ"
     *  }
     *
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {
     *        "status": 200,
     *        "message": "OK",
     *        "data": {
                "bookingId": "XXXYYYZ",
                "quote_gid" : "48c82774ead469ad311c1e6112562726",
                "key": "51_U1NTMTAxKlkxMDAwL0pGS05CTzIwMjItMDEtMTAvTkJPSkZLMjAyMi0wMS0zMSp+I0VUNTEzI0VUMzA4I0VUMzA5I0VUNTEyfmxjOmVuX3VzOkVYXzE3Yz123456789",
                "prices": {
                    "totalPrice": 332.12,
                    "comm": 0,
                    "isCk": false
                },
                "passengers": {
                    "ADT": {
                        "codeAs": "JCB",
                        "cnt": 1,
                        "baseFare": 32.12,
                        "pubBaseFare": 32.12,
                        "baseTax": 300,
                        "markup": 0,
                        "comm": 0,
                        "price": 332.12,
                        "tax": 300,
                        "oBaseFare": {
                            "amount": 32.120003,
                            "currency": "USD"
                        },
                        "oBaseTax": {
                            "amount": 300,
                            "currency": "USD"
                        },
                        "oExchangeFareDiff": {
                            "amount": 8,
                            "currency": "USD"
                        },
                        "oExchangeTaxDiff": {
                            "amount": 24.12,
                            "currency": "USD"
                        }
                    }
                },
                "trips": [
                    {
                        "tripId": 1,
                        "segments": [
                            {
                                "segmentId": 1,
                                "departureTime": "2022-01-10 20:15",
                                "arrivalTime": "2022-01-11 21:10",
                                "stop": 1,
                                "stops": [
                                    {
                                        "locationCode": "LFW",
                                        "departureDateTime": "2022-01-11 12:35",
                                        "arrivalDateTime": "2022-01-11 11:35",
                                        "duration": 60,
                                        "elapsedTime": 620,
                                        "equipment": "787"
                                    }
                                ],
                                "flightNumber": "513",
                                "bookingClass": "H",
                                "duration": 1015,
                                "departureAirportCode": "JFK",
                                "departureAirportTerminal": "8",
                                "arrivalAirportCode": "ADD",
                                "arrivalAirportTerminal": "2",
                                "operatingAirline": "ET",
                                "airEquipType": "787",
                                "marketingAirline": "ET",
                                "marriageGroup": "O",
                                "cabin": "Y",
                                "meal": "DL",
                                "fareCode": "HLESUS",
                                "recheckBaggage": false
                            },
                            {
                                "segmentId": 2,
                                "departureTime": "2022-01-11 23:15",
                                "arrivalTime": "2022-01-12 01:20",
                                "stop": 0,
                                "stops": null,
                                "flightNumber": "308",
                                "bookingClass": "H",
                                "duration": 125,
                                "departureAirportCode": "ADD",
                                "departureAirportTerminal": "2",
                                "arrivalAirportCode": "NBO",
                                "arrivalAirportTerminal": "1C",
                                "operatingAirline": "ET",
                                "airEquipType": "738",
                                "marketingAirline": "ET",
                                "marriageGroup": "I",
                                "cabin": "Y",
                                "meal": "D",
                                "fareCode": "HLESUS",
                                "recheckBaggage": false
                            }
                        ],
                        "duration": 1265
                    },
                    {
                        "tripId": 2,
                        "segments": [
                            {
                                "segmentId": 1,
                                "departureTime": "2022-01-31 05:00",
                                "arrivalTime": "2022-01-31 07:15",
                                "stop": 0,
                                "stops": null,
                                "flightNumber": "309",
                                "bookingClass": "E",
                                "duration": 135,
                                "departureAirportCode": "NBO",
                                "departureAirportTerminal": "1C",
                                "arrivalAirportCode": "ADD",
                                "arrivalAirportTerminal": "2",
                                "operatingAirline": "ET",
                                "airEquipType": "738",
                                "marketingAirline": "ET",
                                "marriageGroup": "O",
                                "cabin": "Y",
                                "meal": "B",
                                "fareCode": "ELPRUS",
                                "recheckBaggage": false
                            },
                            {
                                "segmentId": 2,
                                "departureTime": "2022-01-31 08:30",
                                "arrivalTime": "2022-01-31 18:15",
                                "stop": 1,
                                "stops": [
                                    {
                                        "locationCode": "LFW",
                                        "departureDateTime": "2022-01-31 12:15",
                                        "arrivalDateTime": "2022-01-31 11:00",
                                        "duration": 75,
                                        "elapsedTime": 330,
                                        "equipment": "787"
                                    }
                                ],
                                "flightNumber": "512",
                                "bookingClass": "E",
                                "duration": 1065,
                                "departureAirportCode": "ADD",
                                "departureAirportTerminal": "2",
                                "arrivalAirportCode": "JFK",
                                "arrivalAirportTerminal": "8",
                                "operatingAirline": "ET",
                                "airEquipType": "787",
                                "marketingAirline": "ET",
                                "marriageGroup": "I",
                                "cabin": "Y",
                                "meal": "LD",
                                "fareCode": "ELPRUS",
                                "recheckBaggage": false
                            }
                        ],
                        "duration": 1275
                    }
                ],
                "paxCnt": 1,
                "validatingCarrier": "",
                "gds": "S",
                "pcc": "G9MJ",
                "cons": "GTT",
                "fareType": "SR",
                "cabin": "Y",
                "currency": "USD",
                "currencies": [
                    "USD"
                ],
                "currencyRates": {
                    "USDUSD": {
                        "from": "USD",
                        "to": "USD",
                        "rate": 1
                    }
                },
                "keys": {},
                "meta": {
                    "eip": 0,
                    "noavail": false,
                    "searchId": "U1NTMTAxWTEwMDB8SkZLTkJPMjAyMi0wMS0xMHxOQk9KRksyMDIyLTAxLTMx",
                    "lang": "en",
                    "rank": 0,
                    "cheapest": false,
                    "fastest": false,
                    "best": false,
                    "country": "us"
                },
                "billing": {
                      "first_name": "John",
                      "last_name": "Doe",
                      "middle_name": "",
                      "address_line1": "1013 Weda Cir",
                      "address_line2": "",
                      "country_id": "US",
                      "city": "Mayfield",
                      "state": "KY",
                      "zip": "99999",
                      "company_name": "",
                      "contact_phone": "+19074861000",
                      "contact_email": "test@test.com",
                      "contact_name": "Test Name"
                },
                "payment_request": {
                      "method_key": "cc",
                      "currency": "USD",
                      "method_data": {
                          "card": {
                              "number": "4111555577778888",
                              "holder_name": "Test test",
                              "expiration_month": 10,
                              "expiration_year": 23,
                              "cvv": "1234"
                          }
                      },
                      "amount": 112.25
                }
            },
     *        "code": "13200",
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
     *        "code": "13106",
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
     *            "booking_id": [
     *               "booking_id cannot be blank."
     *             ]
     *        ],
     *        "code": "13107",
     *        "technical": {
     *           ...
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     */
    public function actionInfo()
    {
        try {
            $post = Yii::$app->request->post();
        } catch (\Throwable $throwable) {
            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::BAD_REQUEST),
                new MessageMessage(Messages::POST_DATA_ERROR),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage(ApiCodeException::POST_DATA_NOT_LOADED)
            );
        }

        $voluntaryExchangeInfoForm = new VoluntaryExchangeInfoForm();
        if (!$voluntaryExchangeInfoForm->load($post)) {
            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::BAD_REQUEST),
                new ErrorsMessage(Messages::LOAD_DATA_ERROR),
                new CodeMessage(ApiCodeException::POST_DATA_NOT_LOADED)
            );
        }
        if (!$voluntaryExchangeInfoForm->validate()) {
            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY),
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($voluntaryExchangeInfoForm->getErrors()),
                new CodeMessage(ApiCodeException::FAILED_FORM_VALIDATE)
            );
        }

        try {
            $productQuoteChange = VoluntaryExchangeInfoService::getLastProductQuoteChange($voluntaryExchangeInfoForm->booking_id);
            if (!$productQuoteChange) {
                throw new \RuntimeException(
                    'ProductQuoteChange not found by BookingId(' . $voluntaryExchangeInfoForm->booking_id . ')',
                    ApiCodeException::DATA_NOT_FOUND
                );
            }

            return new SuccessResponse(
                new DataMessage(ArrayHelper::toArray($productQuoteChange->pqc_data_json)),
                new CodeMessage(ApiCodeException::SUCCESS)
            );
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['post'] = $post;
            $message['apiUser'] = [
                'username' => $this->auth->au_api_username ?? null,
                'project' => $this->auth->auProject->project_key ?? null,
            ];
            \Yii::warning($message, 'FlightQuoteExchangeController:actionInfo:Warning');

            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage($throwable->getCode())
            );
        } catch (\Throwable $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['post'] = $post;
            $message['apiUser'] = [
                'username' => $this->auth->au_api_username ?? null,
                'project' => $this->auth->auProject->project_key ?? null,
            ];
            \Yii::error($message, 'FlightQuoteExchangeController:actionInfo:Throwable');

            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::INTERNAL_SERVER_ERROR),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage($throwable->getCode())
            );
        }
    }

    /**
     * @api {post} /v2/flight-quote-exchange/get-change Flight Voluntary Product Quote Change Info
     * @apiVersion 0.2.0
     * @apiName Flight Voluntary Product Quote Change
     * @apiGroup Flight Voluntary Exchange
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{32}}    change_gid          Change gid
     *
     * @apiParamExample {json} Request-Example:
     *  {
     *      "change_gid": "16b2506459becec5e038b829568de2bb"
     *  }
     *
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {
     *        "status": 200,
     *        "message": "OK",
     *        "data": {
                "bookingId": "XXXYYYZ",
                "quote_gid" : "48c82774ead469ad311c1e6112562726",
                "key": "51_U1NTMTAxKlkxMDAwL0pGS05CTzIwMjItMDEtMTAvTkJPSkZLMjAyMi0wMS0zMSp+I0VUNTEzI0VUMzA4I0VUMzA5I0VUNTEyfmxjOmVuX3VzOkVYXzE3Yz123456789",
                "prices": {
                    "totalPrice": 332.12,
                    "comm": 0,
                    "isCk": false
                },
                "passengers": {
                    "ADT": {
                        "codeAs": "JCB",
                        "cnt": 1,
                        "baseFare": 32.12,
                        "pubBaseFare": 32.12,
                        "baseTax": 300,
                        "markup": 0,
                        "comm": 0,
                        "price": 332.12,
                        "tax": 300,
                        "oBaseFare": {
                            "amount": 32.120003,
                            "currency": "USD"
                        },
                        "oBaseTax": {
                            "amount": 300,
                            "currency": "USD"
                        },
                        "oExchangeFareDiff": {
                            "amount": 8,
                            "currency": "USD"
                        },
                        "oExchangeTaxDiff": {
                            "amount": 24.12,
                            "currency": "USD"
                        }
                    }
                },
                "trips": [
                    {
                        "tripId": 1,
                        "segments": [
                            {
                                "segmentId": 1,
                                "departureTime": "2022-01-10 20:15",
                                "arrivalTime": "2022-01-11 21:10",
                                "stop": 1,
                                "stops": [
                                    {
                                        "locationCode": "LFW",
                                        "departureDateTime": "2022-01-11 12:35",
                                        "arrivalDateTime": "2022-01-11 11:35",
                                        "duration": 60,
                                        "elapsedTime": 620,
                                        "equipment": "787"
                                    }
                                ],
                                "flightNumber": "513",
                                "bookingClass": "H",
                                "duration": 1015,
                                "departureAirportCode": "JFK",
                                "departureAirportTerminal": "8",
                                "arrivalAirportCode": "ADD",
                                "arrivalAirportTerminal": "2",
                                "operatingAirline": "ET",
                                "airEquipType": "787",
                                "marketingAirline": "ET",
                                "marriageGroup": "O",
                                "cabin": "Y",
                                "meal": "DL",
                                "fareCode": "HLESUS",
                                "recheckBaggage": false
                            },
                            {
                                "segmentId": 2,
                                "departureTime": "2022-01-11 23:15",
                                "arrivalTime": "2022-01-12 01:20",
                                "stop": 0,
                                "stops": null,
                                "flightNumber": "308",
                                "bookingClass": "H",
                                "duration": 125,
                                "departureAirportCode": "ADD",
                                "departureAirportTerminal": "2",
                                "arrivalAirportCode": "NBO",
                                "arrivalAirportTerminal": "1C",
                                "operatingAirline": "ET",
                                "airEquipType": "738",
                                "marketingAirline": "ET",
                                "marriageGroup": "I",
                                "cabin": "Y",
                                "meal": "D",
                                "fareCode": "HLESUS",
                                "recheckBaggage": false
                            }
                        ],
                        "duration": 1265
                    },
                    {
                        "tripId": 2,
                        "segments": [
                            {
                                "segmentId": 1,
                                "departureTime": "2022-01-31 05:00",
                                "arrivalTime": "2022-01-31 07:15",
                                "stop": 0,
                                "stops": null,
                                "flightNumber": "309",
                                "bookingClass": "E",
                                "duration": 135,
                                "departureAirportCode": "NBO",
                                "departureAirportTerminal": "1C",
                                "arrivalAirportCode": "ADD",
                                "arrivalAirportTerminal": "2",
                                "operatingAirline": "ET",
                                "airEquipType": "738",
                                "marketingAirline": "ET",
                                "marriageGroup": "O",
                                "cabin": "Y",
                                "meal": "B",
                                "fareCode": "ELPRUS",
                                "recheckBaggage": false
                            },
                            {
                                "segmentId": 2,
                                "departureTime": "2022-01-31 08:30",
                                "arrivalTime": "2022-01-31 18:15",
                                "stop": 1,
                                "stops": [
                                    {
                                        "locationCode": "LFW",
                                        "departureDateTime": "2022-01-31 12:15",
                                        "arrivalDateTime": "2022-01-31 11:00",
                                        "duration": 75,
                                        "elapsedTime": 330,
                                        "equipment": "787"
                                    }
                                ],
                                "flightNumber": "512",
                                "bookingClass": "E",
                                "duration": 1065,
                                "departureAirportCode": "ADD",
                                "departureAirportTerminal": "2",
                                "arrivalAirportCode": "JFK",
                                "arrivalAirportTerminal": "8",
                                "operatingAirline": "ET",
                                "airEquipType": "787",
                                "marketingAirline": "ET",
                                "marriageGroup": "I",
                                "cabin": "Y",
                                "meal": "LD",
                                "fareCode": "ELPRUS",
                                "recheckBaggage": false
                            }
                        ],
                        "duration": 1275
                    }
                ],
                "paxCnt": 1,
                "validatingCarrier": "",
                "gds": "S",
                "pcc": "G9MJ",
                "cons": "GTT",
                "fareType": "SR",
                "cabin": "Y",
                "currency": "USD",
                "currencies": [
                    "USD"
                ],
                "currencyRates": {
                    "USDUSD": {
                        "from": "USD",
                        "to": "USD",
                        "rate": 1
                    }
                },
                "keys": {},
                "meta": {
                    "eip": 0,
                    "noavail": false,
                    "searchId": "U1NTMTAxWTEwMDB8SkZLTkJPMjAyMi0wMS0xMHxOQk9KRksyMDIyLTAxLTMx",
                    "lang": "en",
                    "rank": 0,
                    "cheapest": false,
                    "fastest": false,
                    "best": false,
                    "country": "us"
                },
                "billing": {
                      "first_name": "John",
                      "last_name": "Doe",
                      "middle_name": "",
                      "address_line1": "1013 Weda Cir",
                      "address_line2": "",
                      "country_id": "US",
                      "city": "Mayfield",
                      "state": "KY",
                      "zip": "99999",
                      "company_name": "",
                      "contact_phone": "+19074861000",
                      "contact_email": "test@test.com",
                      "contact_name": "Test Name"
                },
                "payment_request": {
                      "method_key": "cc",
                      "currency": "USD",
                      "method_data": {
                          "card": {
                              "number": "4111555577778888",
                              "holder_name": "Test test",
                              "expiration_month": 10,
                              "expiration_year": 23,
                              "cvv": "1234"
                          }
                      },
                      "amount": 112.25
                }
            },
     *        "code": "13200",
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
     *        "code": "13106",
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
     *            "booking_id": [
     *               "booking_id cannot be blank."
     *             ]
     *        ],
     *        "code": "13107",
     *        "technical": {
     *           ...
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     */
    public function actionGetChange()
    {
        try {
            $post = Yii::$app->request->post();
        } catch (\Throwable $throwable) {
            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::BAD_REQUEST),
                new MessageMessage(Messages::POST_DATA_ERROR),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage(ApiCodeException::POST_DATA_NOT_LOADED)
            );
        }

        /* TODO:: FOR DEBUG:: must by remove  */
        return new ErrorResponse(
            new StatusCodeMessage(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY),
            new ErrorsMessage('Endpoint is under construction'),
            new CodeMessage(0)
        );

        $voluntaryExchangeInfoForm = new VoluntaryExchangeInfoForm();
        if (!$voluntaryExchangeInfoForm->load($post)) {
            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::BAD_REQUEST),
                new ErrorsMessage(Messages::LOAD_DATA_ERROR),
                new CodeMessage(ApiCodeException::POST_DATA_NOT_LOADED)
            );
        }
        if (!$voluntaryExchangeInfoForm->validate()) {
            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY),
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($voluntaryExchangeInfoForm->getErrors()),
                new CodeMessage(ApiCodeException::FAILED_FORM_VALIDATE)
            );
        }

        try {
            $productQuoteChange = VoluntaryExchangeInfoService::getLastProductQuoteChange($voluntaryExchangeInfoForm->booking_id);
            if (!$productQuoteChange) {
                throw new \RuntimeException(
                    'ProductQuoteChange not found by BookingId(' . $voluntaryExchangeInfoForm->booking_id . ')',
                    ApiCodeException::DATA_NOT_FOUND
                );
            }

            return new SuccessResponse(
                new DataMessage(ArrayHelper::toArray($productQuoteChange->pqc_data_json)),
                new CodeMessage(ApiCodeException::SUCCESS)
            );
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['post'] = $post;
            $message['apiUser'] = [
                'username' => $this->auth->au_api_username ?? null,
                'project' => $this->auth->auProject->project_key ?? null,
            ];
            \Yii::warning($message, 'FlightQuoteExchangeController:actionInfo:Warning');

            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage($throwable->getCode())
            );
        } catch (\Throwable $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['post'] = $post;
            $message['apiUser'] = [
                'username' => $this->auth->au_api_username ?? null,
                'project' => $this->auth->auProject->project_key ?? null,
            ];
            \Yii::error($message, 'FlightQuoteExchangeController:actionInfo:Throwable');

            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::INTERNAL_SERVER_ERROR),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage($throwable->getCode())
            );
        }
    }

    /**
     * @api {post} /v2/flight-quote-exchange/view Flight Voluntary Exchange View
     * @apiVersion 0.2.0
     * @apiName Flight Voluntary Exchange View
     * @apiGroup Flight Voluntary Exchange
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
     *
     * @apiParamExample {json} Request-Example:
     *  {
     *      "booking_id": "XXXYYYZ"
     *  }
     *
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {
     *        "status": 200,
     *        "message": "OK",
     *        "data": {
                    "productQuoteChange": {
                        "id": 950326,
                        "productQuoteId": 950326,
                        "productQuoteGid": "b1ae27497b6eaab24a39fc1370069bd4",
                        "caseId": 35618,
                        "caseGid": "e7dce13b4e6a5f3ccc2cec9c21fa3255",
                        "statusId": 4,
                        "statusName": "Complete",
                        "decisionTypeId": null,
                        "decisionTypeName": "Undefined",
                        "isAutomate": 1,
                        "createdDt": "2021-09-21 03:28:33",
                        "updatedDt": "2021-09-28 09:11:38"
                    }
               },
     *        "code": "13200",
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
     *        "code": "13106",
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
     *            "booking_id": [
     *               "booking_id cannot be blank."
     *             ]
     *        ],
     *        "code": "13107",
     *        "technical": {
     *           ...
     *        },
     *        "request": {
     *           ...
     *        }
     * }
     */
    public function actionView()
    {
        try {
            $post = Yii::$app->request->post();
        } catch (\Throwable $throwable) {
            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::BAD_REQUEST),
                new MessageMessage(Messages::POST_DATA_ERROR),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage(ApiCodeException::POST_DATA_NOT_LOADED)
            );
        }

        $voluntaryExchangeInfoForm = new VoluntaryExchangeInfoForm();
        if (!$voluntaryExchangeInfoForm->load($post)) {
            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::BAD_REQUEST),
                new ErrorsMessage(Messages::LOAD_DATA_ERROR),
                new CodeMessage(ApiCodeException::POST_DATA_NOT_LOADED)
            );
        }
        if (!$voluntaryExchangeInfoForm->validate()) {
            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY),
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($voluntaryExchangeInfoForm->getErrors()),
                new CodeMessage(ApiCodeException::FAILED_FORM_VALIDATE)
            );
        }

        try {
            $productQuoteChange = VoluntaryExchangeInfoService::getLastProductQuoteChange($voluntaryExchangeInfoForm->booking_id);
            if (!$productQuoteChange) {
                throw new \RuntimeException(
                    'ProductQuoteChange not found by BookingId(' . $voluntaryExchangeInfoForm->booking_id . ')',
                    ApiCodeException::DATA_NOT_FOUND
                );
            }

            return new SuccessResponse(
                new DataMessage([
                    'productQuoteChange' => $productQuoteChange
                        ->setFields(VoluntaryExchangeInfoService::apiDataMapper($productQuoteChange))
                        ->toArray(),
                ]),
                new CodeMessage(ApiCodeException::SUCCESS)
            );
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['post'] = $post;
            $message['apiUser'] = [
                'username' => $this->auth->au_api_username ?? null,
                'project' => $this->auth->auProject->project_key ?? null,
            ];
            \Yii::warning($message, 'FlightQuoteExchangeController:actionInfo:Warning');

            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage($throwable->getCode())
            );
        } catch (\Throwable $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['post'] = $post;
            $message['apiUser'] = [
                'username' => $this->auth->au_api_username ?? null,
                'project' => $this->auth->auProject->project_key ?? null,
            ];
            \Yii::error($message, 'FlightQuoteExchangeController:actionInfo:Throwable');

            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::INTERNAL_SERVER_ERROR),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage($throwable->getCode())
            );
        }
    }
}

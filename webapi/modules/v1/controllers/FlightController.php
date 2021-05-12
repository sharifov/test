<?php

namespace webapi\modules\v1\controllers;

use common\models\ApiLog;
use modules\flight\models\FlightQuote;
use modules\flight\src\exceptions\FlightCodeException;
use modules\flight\src\forms\api\FlightFailRequestApiForm;
use modules\flight\src\forms\TicketFlightsForm;
use modules\flight\src\repositories\flightQuoteRepository\FlightQuoteRepository;
use modules\flight\src\services\api\FlightUpdateRequestApiService;
use modules\flight\src\services\api\TicketIssueProcessingDataService;
use modules\order\src\payment\PaymentRepository;
use modules\order\src\processManager\clickToBook\events\FlightProductProcessedErrorEvent;
use modules\order\src\processManager\clickToBook\events\FlightProductProcessedSuccessEvent;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\exceptions\ProductCodeException;
use modules\product\src\services\productQuote\ProductQuoteReplaceService;
use sales\helpers\app\AppHelper;
use sales\helpers\ErrorsToStringHelper;
use sales\repositories\product\ProductQuoteRepository;
use sales\services\TransactionManager;
use webapi\src\ApiCodeException;
use webapi\src\forms\flight\FlightRequestApiForm;
use webapi\src\forms\payment\PaymentFromBoForm;
use webapi\src\Messages;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\DataMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\Response;
use webapi\src\response\SuccessResponse;
use webapi\src\services\flight\FlightManageApiService;
use webapi\src\services\payment\PaymentManageApiService;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use sales\dispatchers\EventDispatcher;

/**
 * Class FlightController
 * @property FlightQuoteRepository $flightQuoteRepository
 * @property ProductQuoteRepository $productQuoteRepository
 * @property TransactionManager $transactionManager
 * @property PaymentRepository $paymentRepository
 * @property TicketIssueProcessingDataService $ticketIssueProcessingDataService
 * @property EventDispatcher $eventDispatcher
 * @property PaymentManageApiService $paymentManageApiService
 * @property FlightManageApiService $flightManageApiService
 * @property ProductQuoteReplaceService $productQuoteReplaceService
 */
class FlightController extends ApiBaseController
{
    private FlightQuoteRepository $flightQuoteRepository;
    private ProductQuoteRepository $productQuoteRepository;
    private TransactionManager $transactionManager;
    private PaymentRepository $paymentRepository;
    private TicketIssueProcessingDataService $ticketIssueProcessingDataService;
    private EventDispatcher $eventDispatcher;
    private PaymentManageApiService $paymentManageApiService;
    private FlightManageApiService $flightManageApiService;
    private ProductQuoteReplaceService $productQuoteReplaceService;

    public function __construct(
        $id,
        $module,
        FlightQuoteRepository $flightQuoteRepository,
        ProductQuoteRepository $productQuoteRepository,
        TransactionManager $transactionManager,
        PaymentRepository $paymentRepository,
        TicketIssueProcessingDataService $ticketIssueProcessingDataService,
        EventDispatcher $eventDispatcher,
        PaymentManageApiService $paymentManageApiService,
        FlightManageApiService $flightManageApiService,
        ProductQuoteReplaceService $productQuoteReplaceService,
        $config = []
    ) {
        $this->flightQuoteRepository = $flightQuoteRepository;
        $this->productQuoteRepository = $productQuoteRepository;
        $this->transactionManager = $transactionManager;
        $this->paymentRepository = $paymentRepository;
        $this->ticketIssueProcessingDataService = $ticketIssueProcessingDataService;
        $this->eventDispatcher = $eventDispatcher;
        $this->paymentManageApiService = $paymentManageApiService;
        $this->flightManageApiService = $flightManageApiService;
        $this->productQuoteReplaceService = $productQuoteReplaceService;

        parent::__construct($id, $module, $config);
    }

    public function actionTicket()
    {
        $apiLog = $this->startApiLog($this->action->uniqueId);
        return $this->endApiLog($apiLog, new ErrorResponse(
            new StatusCodeMessage(400),
            new MessageMessage('This endpoint has been replaced with "/v1/flight/ticket-issue", "/v1/flight/replace", "/v1/flight/fail"'),
        ));
    }

    /**
     * @api {post} /v1/flight/replace Flight Replace
     * @apiVersion 0.1.0
     * @apiName Flight Replace
     * @apiGroup Flight
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeader {string} Accept-Encoding
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{15}}   fareId                      Fare Id (Order identity)
     * @apiParam {object}       flights                     Flights data array
     * @apiParam {object}       [payments]                  Payments data array
     * @apiParam {object}       [options]                   Options data array
     *
     * @apiParamExample {json} Request-Example:
     *
     *   {
            "fareId": "or6061be5ec5c0e",
            "parentBookingId": "OE96040",
            "parentId": 205975,
            "sameItinerary": true,
            "flights": [
                {
                    "appKey": "038ce0121a1666678d4db57cb10e8667b98d8b08c408cdf7c9b04f1430071826",
                    "uniqueId": "OE96040",
                    "status": 6,
                    "pnr": "",
                    "gds": "",
                    "flightType": "RT",
                    "validatingCarrier": "PR",
                    "bookingInfo": [
                        {
                            "bookingId": "OE96040",
                            "pnr": "Q3PM1G",
                            "gds": "S",
                            "validatingCarrier": "PR",
                            "status": 6,
                            "state": "Rejected",
                            "passengers": {
                                "1": {
                                    "fullName": "Arthur Davis",
                                    "first_name": "Arthur",
                                    "middle_name": "",
                                    "last_name": "Davis",
                                    "birth_date": "1963-04-07",
                                    "nationality": "US",
                                    "gender": "M",
                                    "aGender": "Mr.",
                                    "tktNumber": null,
                                    "paxType": "ADT"
                                }
                            },
                            "airlinesCode": [
                                {
                                    "code": "PR",
                                    "airline": "Philippine Airlines",
                                    "recordLocator": "Q3PM1G"
                                }
                            ],
                            "insurance": []
                        }
                    ],
                    "trips": [
                        {
                            "segments": [
                                {
                                    "segmentId": 1001959,
                                    "passengers":{
                                        "p1":{
                                            "fullname":"Tester Testerov",
                                            "products":{
                                                "bag":"test_30kg",
                                                "seat":"24E"
                                            }
                                        }
                                    },
                                    "airline": "PR",
                                    "airlineName": "Philippine Airlines",
                                    "mainAirline": "PR",
                                    "arrivalAirport": "MNL",
                                    "arrivalTime": "2021-05-15 04:00:00",
                                    "departureAirport": "LAX",
                                    "departureTime": "2021-05-13 22:30:00",
                                    "bookingClass": "U",
                                    "flightNumber": 103,
                                    "statusCode": "HK",
                                    "operatingAirline": "Philippine Airlines",
                                    "operatingAirlineCode": "PR",
                                    "cabin": "Economy",
                                    "departureCity": "Los Angeles",
                                    "arrivalCity": "Manila",
                                    "departureCountry": "US",
                                    "arrivalCountry": "PH",
                                    "departureAirportName": "Los Angeles International Airport",
                                    "arrivalAirportName": "Ninoy Aquino International Airport",
                                    "flightDuration": 870,
                                    "layoverDuration": 0,
                                    "airlineRecordLocator": "Q3PM1G",
                                    "aircraft": "773",
                                    "baggage": 2,
                                    "carryOn": true,
                                    "marriageGroup": "773",
                                    "fareCode": "U9XBUS",
                                    "mileage": 7305
                                },
                                {
                                    "segmentId": 1001960,
                                    "passengers":{
                                        "p1":{
                                            "fullname":"Tester Testerov",
                                            "products":{
                                                "bag":"test_30kg",
                                                "seat":"25E"
                                            }
                                        }
                                    },
                                    "airline": "PR",
                                    "airlineName": "Philippine Airlines",
                                    "mainAirline": "PR",
                                    "arrivalAirport": "TPE",
                                    "arrivalTime": "2021-05-15 08:40:00",
                                    "departureAirport": "MNL",
                                    "departureTime": "2021-05-15 06:30:00",
                                    "bookingClass": "U",
                                    "flightNumber": 890,
                                    "statusCode": "HK",
                                    "operatingAirline": "Philippine Airlines",
                                    "operatingAirlineCode": "PR",
                                    "cabin": "Economy",
                                    "departureCity": "Manila",
                                    "arrivalCity": "Taipei",
                                    "departureCountry": "PH",
                                    "arrivalCountry": "TW",
                                    "departureAirportName": "Ninoy Aquino International Airport",
                                    "arrivalAirportName": "Taiwan Taoyuan International Airport",
                                    "flightDuration": 130,
                                    "layoverDuration": 150,
                                    "airlineRecordLocator": "Q3PM1G",
                                    "aircraft": "321",
                                    "baggage": 2,
                                    "carryOn": true,
                                    "marriageGroup": "321",
                                    "fareCode": "U9XBUS",
                                    "mileage": 728
                                }
                            ]
                        }
                    ],
                    "price": {
                        "tickets": 1,
                        "selling": 767.75,
                        "currentProfit": 0,
                        "fare": 446,
                        "net": 717.75,
                        "taxes": 321.75,
                        "tips": 0,
                        "currency": "USD",
                        "detail": {
                            "ADT": {
                                "selling": 767.75,
                                "fare": 446,
                                "baseTaxes": 271.75,
                                "taxes": 321.75,
                                "tickets": 1,
                                "insurance": 0
                            }
                        }
                    },
                    "departureTime": "2021-05-13 22:30:00",
                    "invoiceUri": "\/checkout\/download\/OE96040\/invoice",
                    "eTicketUri": "\/checkout\/download\/OE96040\/e-ticket",
                    "scheduleChange": "No"
                }
            ],
            "trips": [],
            "payments": [
                {
                    "pay_amount": 200.21,
                    "pay_currency": "USD",
                    "pay_auth_id": 728282,
                    "pay_type": "Capture",
                    "pay_code": "ch_YYYYYYYYYYYYYYYYYYYYY",
                    "pay_date": "2021-03-25",
                    "pay_method_key": "card",
                    "pay_description": "example description",
                    "creditCard": {
                        "holder_name": "Tester holder",
                        "number": "111**********111",
                        "type": "Visa",
                        "expiration": "07 / 23",
                        "cvv": "123"
                    },
                    "billingInfo": {
                        "first_name": "Hobbit",
                        "middle_name": "Hard",
                        "last_name": "Lover",
                        "address": "1013 Weda Cir",
                        "country_id": "US",
                        "city": "Gotham City",
                        "state": "KY",
                        "zip": "99999",
                        "phone": "+19074861000",
                        "email": "barabara@test.com"
                    }
                }
            ],
            "options": [
                {
                  "pqo_key": "cfar",
                  "pqo_name": "CFAR option",
                  "pqo_price": 750.21,
                  "pqo_markup": 100.21,
                  "pqo_description": "CFAR option: Cancel before limit",
                  "pqo_request_data": "{\"type\":\"standard\",\"amount\":750.21,\"options\":[{\"name\":\"Cancel before limit\",\"type\":\"before\",\"limit\":0,\"value\":\"60\"}],\"paxCount\":3,\"isActivated\":true,\"amountPerPax\":250.07}"
                },
                {
                  "pqo_key": "package",
                  "pqo_name": "Package option",
                  "pqo_price": 89.85,
                  "pqo_markup": 0,
                  "pqo_description": "Package option: Exchange and Refund Processing Fee",
                  "pqo_request_data": "{\"type\":\"standard\",\"amount\":89.85,\"options\":[{\"name\":\"24 Hour Free Cancellation\",\"type\":\"VOID\",\"value\":\"included\",\"special\":true}],\"paxCount\":3,\"isActivated\":true,\"amountPerPax\":29.95}"
                }
            ]
        }
     *
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {
     *      "status": 200,
     *      "message": "OK",
     *      "data": {
     *          "resultMessage": "Order Uid(or6061be5ec5c0e) successful processed"
     *      }
     * }
     *
     * @apiErrorExample {json} Error-Response (422):
     * HTTP/1.1 200 OK
     * {
     *     "status": 422,
     *     "message": "Validation error",
     *     "errors": {
     *         "orderUid": [
     *             "orderUid cannot be blank"
     *        ]
     *     },
     *     "code": "15801"
     * }
     *
     * @apiErrorExample {json} Error-Response (404):
     * HTTP/1.1 200 OK
     * {
     *     "status": 404,
     *     "message": "Order not found",
     *     "code": "15300",
     *     "errors": []
     * }
     */
    public function actionReplace()
    {
        $apiLog = $this->startApiLog($this->action->uniqueId);

        if (!Yii::$app->request->isPost) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('Not found POST request'),
                new CodeMessage(ApiCodeException::REQUEST_IS_NOT_POST)
            );
        }
        if (!Yii::$app->request->post()) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('POST data request is empty'),
                new CodeMessage(ApiCodeException::POST_DATA_IS_EMPTY)
            );
        }

        $dataMessage = [];
        $post = Yii::$app->request->post();
        $flightRequestApiForm = new FlightRequestApiForm();
        if (!$flightRequestApiForm->load($post)) {
            return $this->endApiLog($apiLog, self::notLoadErrorResponse('FlightTicketIssueRequestApiForm'));
        }
        if (!$flightRequestApiForm->validate()) {
            \Yii::warning(
                ErrorsToStringHelper::extractFromModel($flightRequestApiForm),
                'FlightController:actionReplace:FlightTicketIssueRequestApiForm'
            );
            return $this->endApiLog($apiLog, self::validateErrorResponse($flightRequestApiForm));
        }

        try {
            $originFlightQuote = $flightRequestApiForm->flightQuote;
            $originProductQuote = $originFlightQuote->fqProductQuote;

            $this->transactionManager->wrap(function () use ($flightRequestApiForm, $originProductQuote, $originFlightQuote) {
                if ($this->flightManageApiService->checkProcessedStatus($flightRequestApiForm)) {
                    $newProductQuote = $this->productQuoteReplaceService->replaceFromApiBo($originProductQuote->pq_id, $originFlightQuote);
                    if (!$newFlightQuote = $newProductQuote->flightQuote) {
                        throw new \DomainException('FlightQuote not created');
                    }

                    $this->flightManageApiService->replaceProcessing($flightRequestApiForm, $newFlightQuote);
                    $this->flightManageApiService->recalculatePricingProcessing($newProductQuote, $newFlightQuote);

                    $this->eventDispatcher->dispatch(new FlightProductProcessedSuccessEvent($flightRequestApiForm->order->or_id));
                } else {
                    $this->ticketIssueProcessingDataService->failQuote($flightRequestApiForm->order);
                    $this->eventDispatcher->dispatch(new FlightProductProcessedErrorEvent($flightRequestApiForm->order->or_id));
                }
            });

            $dataMessage['orderResult'] = 'Order Gid(' . $flightRequestApiForm->order->or_gid . ') successful processed';
        } catch (\Throwable $throwable) {
            Yii::error(self::errorMessage($throwable, $post), 'FlightController:actionReplace:Throwable');
            return $this->endApiLog($apiLog, new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage($throwable->getMessage()),
                new CodeMessage(FlightCodeException::API_FLIGHT_REPLACE_FAILED)
            ));
        }

        $paymentFromBoForm = new PaymentFromBoForm();
        if (ArrayHelper::keyExists('payments', $post) && $paymentFromBoForm->load($post)) {
            if (!$paymentFromBoForm->validate()) {
                \Yii::warning(
                    ErrorsToStringHelper::extractFromModel($paymentFromBoForm),
                    'FlightController:actionReplace:PaymentFromBoForm'
                );
                $dataMessage['paymentResult'] = 'Payment processing validation failed';
                return $this->endApiLog($apiLog, new ErrorResponse(
                    new MessageMessage(Messages::VALIDATION_ERROR),
                    new ErrorsMessage($paymentFromBoForm->getErrors()),
                    new CodeMessage(FlightCodeException::API_TICKET_FLIGHT_VALIDATE),
                    new DataMessage($dataMessage)
                ));
            }

            try {
                $transactionResult = $this->transactionManager->wrap(function () use ($paymentFromBoForm) {
                    return $this->paymentManageApiService->handler($paymentFromBoForm);
                });
                $dataMessage['paymentResult'] = 'Transaction processed codes(' . implode(',', $transactionResult) . ')';
            } catch (\Throwable $throwable) {
                \Yii::error(
                    ArrayHelper::merge(AppHelper::throwableLog($throwable), $post),
                    'FlightController:actionReplace:PaymentThrowable'
                );
                $dataMessage['paymentResult'] = 'Payment processing failed';
                return $this->endApiLog($apiLog, new ErrorResponse(
                    new StatusCodeMessage(400),
                    new MessageMessage($throwable->getMessage()),
                    new CodeMessage(FlightCodeException::API_TICKET_ISSUE_FAILED),
                    new DataMessage($dataMessage)
                ));
            }
        }

        return $this->endApiLog($apiLog, new SuccessResponse(
            new StatusCodeMessage(200),
            new MessageMessage('OK'),
            new DataMessage($dataMessage)
        ));
    }

    /**
     * @api {post} /v1/flight/ticket-issue Flight Ticket Issue
     * @apiVersion 0.1.0
     * @apiName Flight Ticket Issue
     * @apiGroup Flight
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeader {string} Accept-Encoding
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{255}}          fareId                      Fare Id (Order identity)
     * @apiParam {object}               flights                     Flights data array
     * @apiParam {object}               [payments]                  Payments data array
     * @apiParam {object}               [options]                   Options data array
     *
     * @apiParamExample {json} Request-Example:
     *
     *   {
            "fareId": "or6061be5ec5c0e",
            "parentBookingId": "OE96041",
            "parentId": 205975,
            "sameItinerary": true,
            "flights": [
                {
                    "appKey": "038ce0121a1666678d4db57cb10e8667b98d8b08c408cdf7c9b04f1430071826",
                    "uniqueId": "OE96040",
                    "status": 3,
                    "pnr": "Q3PM1G",
                    "gds": "S",
                    "flightType": "RT",
                    "validatingCarrier": "PR",
                    "bookingInfo": [
                        {
                            "bookingId": "OE96040",
                            "pnr": "Q3PM1G",
                            "gds": "S",
                            "validatingCarrier": "PR",
                            "status": 3,
                            "state": "Success",
                            "passengers": {
                                "1": {
                                    "fullName": "Arthur Davis",
                                    "first_name": "Arthur",
                                    "middle_name": "",
                                    "last_name": "Davis",
                                    "birth_date": "1963-04-07",
                                    "nationality": "US",
                                    "gender": "M",
                                    "aGender": "Mr.",
                                    "tktNumber": "tktNumber",
                                    "paxType": "ADT"
                                }
                            },
                            "airlinesCode": [
                                {
                                    "code": "PR",
                                    "airline": "Philippine Airlines",
                                    "recordLocator": "Q3PM1G"
                                }
                            ],
                            "insurance": []
                        }
                    ],
                    "trips": [
                        {
                            "segments": [
                                {
                                    "segmentId": 1001959,
                                    "passengers":{
                                        "p1":{
                                            "fullname":"Tester Testerov",
                                            "products":{
                                                "bag":"test_30kg",
                                                "seat":"24E"
                                            }
                                        }
                                    },
                                    "airline": "PR",
                                    "airlineName": "Philippine Airlines",
                                    "mainAirline": "PR",
                                    "arrivalAirport": "MNL",
                                    "arrivalTime": "2021-05-15 04:00:00",
                                    "departureAirport": "LAX",
                                    "departureTime": "2021-05-13 22:30:00",
                                    "bookingClass": "U",
                                    "flightNumber": 103,
                                    "statusCode": "HK",
                                    "operatingAirline": "Philippine Airlines",
                                    "operatingAirlineCode": "PR",
                                    "cabin": "Economy",
                                    "departureCity": "Los Angeles",
                                    "arrivalCity": "Manila",
                                    "departureCountry": "US",
                                    "arrivalCountry": "PH",
                                    "departureAirportName": "Los Angeles International Airport",
                                    "arrivalAirportName": "Ninoy Aquino International Airport",
                                    "flightDuration": 870,
                                    "layoverDuration": 0,
                                    "airlineRecordLocator": "Q3PM1G",
                                    "aircraft": "773",
                                    "baggage": 2,
                                    "carryOn": true,
                                    "marriageGroup": "773",
                                    "fareCode": "U9XBUS",
                                    "mileage": 7305
                                },
                                {
                                    "segmentId": 1001960,
                                    "passengers":{
                                        "p1":{
                                            "fullname":"Tester Testerov",
                                            "products":{
                                                "bag":"test_30kg",
                                                "seat":"25E"
                                            }
                                        }
                                    },
                                    "airline": "PR",
                                    "airlineName": "Philippine Airlines",
                                    "mainAirline": "PR",
                                    "arrivalAirport": "TPE",
                                    "arrivalTime": "2021-05-15 08:40:00",
                                    "departureAirport": "MNL",
                                    "departureTime": "2021-05-15 06:30:00",
                                    "bookingClass": "U",
                                    "flightNumber": 890,
                                    "statusCode": "HK",
                                    "operatingAirline": "Philippine Airlines",
                                    "operatingAirlineCode": "PR",
                                    "cabin": "Economy",
                                    "departureCity": "Manila",
                                    "arrivalCity": "Taipei",
                                    "departureCountry": "PH",
                                    "arrivalCountry": "TW",
                                    "departureAirportName": "Ninoy Aquino International Airport",
                                    "arrivalAirportName": "Taiwan Taoyuan International Airport",
                                    "flightDuration": 130,
                                    "layoverDuration": 150,
                                    "airlineRecordLocator": "Q3PM1G",
                                    "aircraft": "321",
                                    "baggage": 2,
                                    "carryOn": true,
                                    "marriageGroup": "321",
                                    "fareCode": "U9XBUS",
                                    "mileage": 728
                                }
                            ]
                        }
                    ],
                    "price": {
                        "tickets": 1,
                        "selling": 767.75,
                        "currentProfit": 0,
                        "fare": 446,
                        "net": 717.75,
                        "taxes": 321.75,
                        "tips": 0,
                        "currency": "USD",
                        "detail": {
                            "ADT": {
                                "selling": 767.75,
                                "fare": 446,
                                "baseTaxes": 271.75,
                                "taxes": 321.75,
                                "tickets": 1,
                                "insurance": 0
                            }
                        }
                    },
                    "departureTime": "2021-05-13 22:30:00",
                    "invoiceUri": "\/checkout\/download\/OE96040\/invoice",
                    "eTicketUri": "\/checkout\/download\/OE96040\/e-ticket",
                    "scheduleChange": "No"
                }
            ],
            "trips": [],
            "payments": [
                {
                    "pay_amount": 200.21,
                    "pay_currency": "USD",
                    "pay_auth_id": 728282,
                    "pay_type": "Capture",
                    "pay_code": "ch_YYYYYYYYYYYYYYYYYYYYY",
                    "pay_date": "2021-03-25",
                    "pay_method_key": "card",
                    "pay_description": "example description",
                    "creditCard": {
                        "holder_name": "Tester holder",
                        "number": "111**********111",
                        "type": "Visa",
                        "expiration": "07 / 23",
                        "cvv": "123"
                    },
                    "billingInfo": {
                        "first_name": "Hobbit",
                        "middle_name": "Hard",
                        "last_name": "Lover",
                        "address": "1013 Weda Cir",
                        "country_id": "US",
                        "city": "Gotham City",
                        "state": "KY",
                        "zip": "99999",
                        "phone": "+19074861000",
                        "email": "barabara@test.com"
                    }
                }
            ],
            "options": [
                {
                  "pqo_key": "cfar",
                  "pqo_name": "CFAR option",
                  "pqo_price": 750.21,
                  "pqo_markup": 100.21,
                  "pqo_description": "CFAR option: Cancel before limit",
                  "pqo_request_data": "{\"type\":\"standard\",\"amount\":750.21,\"options\":[{\"name\":\"Cancel before limit\",\"type\":\"before\",\"limit\":0,\"value\":\"60\"}],\"paxCount\":3,\"isActivated\":true,\"amountPerPax\":250.07}"
                },
                {
                  "pqo_key": "package",
                  "pqo_name": "Package option",
                  "pqo_price": 89.85,
                  "pqo_markup": 0,
                  "pqo_description": "Package option: Exchange and Refund Processing Fee",
                  "pqo_request_data": "{\"type\":\"standard\",\"amount\":89.85,\"options\":[{\"name\":\"24 Hour Free Cancellation\",\"type\":\"VOID\",\"value\":\"included\",\"special\":true}],\"paxCount\":3,\"isActivated\":true,\"amountPerPax\":29.95}"
                }
            ]
        }
     *
     *
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {
     *      "status": 200,
     *      "message": "OK",
     *      "data": {
     *          "resultMessage": "Order Uid(or6061be5ec5c0e) successful processed"
     *      }
     * }
     *
     * @apiErrorExample {json} Error-Response (422):
     * HTTP/1.1 200 OK
     * {
     *     "status": 422,
     *     "message": "Validation error",
     *     "errors": {
     *         "orderUid": [
     *             "orderUid cannot be blank"
     *        ]
     *     },
     *     "code": "15801"
     * }
     *
     * @apiErrorExample {json} Error-Response (404):
     * HTTP/1.1 200 OK
     * {
     *     "status": 404,
     *     "message": "Order not found",
     *     "code": "15300",
     *     "errors": []
     * }
     */
    public function actionTicketIssue()
    {
        $apiLog = $this->startApiLog($this->action->uniqueId);

        if (!Yii::$app->request->isPost) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('Not found POST request'),
                new CodeMessage(ApiCodeException::REQUEST_IS_NOT_POST)
            );
        }
        if (!Yii::$app->request->post()) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('POST data request is empty'),
                new CodeMessage(ApiCodeException::POST_DATA_IS_EMPTY)
            );
        }

        $dataMessage = [];
        $post = Yii::$app->request->post();
        $flightRequestApiForm = new FlightRequestApiForm();
        if (!$flightRequestApiForm->load($post)) {
            return $this->endApiLog($apiLog, self::notLoadErrorResponse('FlightTicketIssueRequestApiForm'));
        }
        if (!$flightRequestApiForm->validate()) {
            \Yii::warning(
                ErrorsToStringHelper::extractFromModel($flightRequestApiForm),
                'FlightController:actionTicketIssue:FlightTicketIssueRequestApiForm'
            );
            return $this->endApiLog($apiLog, self::validateErrorResponse($flightRequestApiForm));
        }

        try {
            $this->transactionManager->wrap(function () use ($flightRequestApiForm) {
                $this->flightManageApiService->ticketIssue($flightRequestApiForm);
                $this->eventDispatcher->dispatch(new FlightProductProcessedSuccessEvent($flightRequestApiForm->order->or_id));
            });
            $dataMessage['orderResult'] = 'Order Gid(' . $flightRequestApiForm->order->or_gid . ') successful processed';
        } catch (\Throwable $throwable) {
            Yii::error(self::errorMessage($throwable, $post), 'FlightController:actionTicketIssue:Throwable');
            return $this->endApiLog($apiLog, new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage($throwable->getMessage()),
                new CodeMessage(FlightCodeException::API_TICKET_ISSUE_FAILED)
            ));
        }

        $paymentFromBoForm = new PaymentFromBoForm();
        if (ArrayHelper::keyExists('payments', $post) && $paymentFromBoForm->load($post)) {
            if (!$paymentFromBoForm->validate()) {
                \Yii::warning(
                    ErrorsToStringHelper::extractFromModel($paymentFromBoForm),
                    'FlightController:actionTicketIssue:PaymentFromBoForm'
                );
                $dataMessage['paymentResult'] = 'Payment processing validation failed';
                return $this->endApiLog($apiLog, new ErrorResponse(
                    new MessageMessage(Messages::VALIDATION_ERROR),
                    new ErrorsMessage($paymentFromBoForm->getErrors()),
                    new CodeMessage(FlightCodeException::API_TICKET_FLIGHT_VALIDATE),
                    new DataMessage($dataMessage)
                ));
            }

            try {
                $transactionResult = $this->transactionManager->wrap(function () use ($paymentFromBoForm) {
                    return $this->paymentManageApiService->handler($paymentFromBoForm);
                });
                $dataMessage['paymentResult'] = 'Transaction processed codes(' . implode(',', $transactionResult) . ')';
            } catch (\Throwable $throwable) {
                \Yii::error(
                    ArrayHelper::merge(AppHelper::throwableLog($throwable), $post),
                    'FlightController:actionTicketIssue:PaymentThrowable'
                );
                $dataMessage['paymentResult'] = 'Payment processing failed';
                return $this->endApiLog($apiLog, new ErrorResponse(
                    new StatusCodeMessage(400),
                    new MessageMessage($throwable->getMessage()),
                    new CodeMessage(FlightCodeException::API_TICKET_ISSUE_FAILED),
                    new DataMessage($dataMessage)
                ));
            }
        }

        return $this->endApiLog($apiLog, new SuccessResponse(
            new StatusCodeMessage(200),
            new MessageMessage('OK'),
            new DataMessage($dataMessage)
        ));
    }

    /**
     * @api {post} /v1/flight/fail Flight Oder Fail
     * @apiVersion 0.1.0
     * @apiName Flight Oder Fail
     * @apiGroup Flight
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeader {string} Accept-Encoding
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{15}}   orderUid       Order Uid
     * @apiParam {string{100}}  [description]  Description
     *
     * @apiParamExample {json} Request-Example:
     *   {
            "orderUid": "or6061be5ec5c0e",
            "description": "Example reason failing"
         }
     *
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     * {
     *      "status": 200,
     *      "message": "OK",
     *      "data": {
     *          "resultMessage": "Order Uid(or6061be5ec5c0e) successful failed"
     *      }
     * }
     *
     * @apiErrorExample {json} Error-Response (422):
     * HTTP/1.1 200 OK
     * {
     *     "status": 422,
     *     "message": "Validation error",
     *     "errors": {
     *         "orderUid": [
     *             "orderUid cannot be blank"
     *        ]
     *     },
     *     "code": "15801"
     * }
     *
     * @apiErrorExample {json} Error-Response (404):
     * HTTP/1.1 200 OK
     * {
     *     "status": 404,
     *     "message": "Order not found",
     *     "code": "15300",
     *     "errors": []
     * }
     */
    public function actionFail()
    {
        $apiLog = $this->startApiLog($this->action->uniqueId);

        if (!Yii::$app->request->isPost) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('Not found POST request'),
                new CodeMessage(ApiCodeException::REQUEST_IS_NOT_POST)
            );
        }
        if (!Yii::$app->request->post()) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('POST data request is empty'),
                new CodeMessage(ApiCodeException::POST_DATA_IS_EMPTY)
            );
        }

        $flightFailRequestApiForm = new FlightFailRequestApiForm();
        $post = Yii::$app->request->post();
        if (!$flightFailRequestApiForm->load($post)) {
            return $this->endApiLog($apiLog, new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found data on request'),
                new CodeMessage(FlightCodeException::API_TICKET_FLIGHT_NOT_FOUND_DATA_ON_REQUEST)
            ));
        }
        if (!$flightFailRequestApiForm->validate()) {
            \Yii::warning(
                ErrorsToStringHelper::extractFromModel($flightFailRequestApiForm),
                'FlightController:actionFail:flightUpdateApiForm'
            );
            return $this->endApiLog($apiLog, new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($flightFailRequestApiForm->getErrors()),
                new CodeMessage(FlightCodeException::API_TICKET_FLIGHT_VALIDATE)
            ));
        }

        try {
            $this->transactionManager->wrap(function () use ($flightFailRequestApiForm) {
                $this->ticketIssueProcessingDataService->failQuote($flightFailRequestApiForm->order);
                $this->eventDispatcher->dispatch(new FlightProductProcessedErrorEvent($flightFailRequestApiForm->order->or_id));
            });

            return $this->endApiLog($apiLog, new SuccessResponse(
                new StatusCodeMessage(200),
                new MessageMessage('OK'),
                new DataMessage([
                    'resultMessage' => 'Order Uid(' . $flightFailRequestApiForm->orderUid . ') successful failed',
                ])
            ));
        } catch (\Throwable $throwable) {
            Yii::error(AppHelper::throwableLog($throwable), 'FlightController:actionFail:FLIGHT_FAIL');
            return $this->endApiLog($apiLog, new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage($throwable->getMessage()),
                new CodeMessage(FlightCodeException::API_FLIGHT_FAIL_FAILED)
            ));
        }
    }

    private function endApiLog(ApiLog $apiLog, Response $response): Response
    {
        $apiLog->endApiLog(ArrayHelper::toArray($response));
        return $response;
    }

    private static function validateErrorResponse(Model $form): ErrorResponse
    {
        return  new ErrorResponse(
            new MessageMessage(Messages::VALIDATION_ERROR),
            new ErrorsMessage($form->getErrors()),
            new CodeMessage(FlightCodeException::API_TICKET_FLIGHT_VALIDATE)
        );
    }

    private static function notLoadErrorResponse(string $formName = ''): ErrorResponse
    {
        return new ErrorResponse(
            new StatusCodeMessage(400),
            new MessageMessage(Messages::LOAD_DATA_ERROR),
            new ErrorsMessage($formName . '. Not found data on request'),
            new CodeMessage(FlightCodeException::API_TICKET_FLIGHT_NOT_FOUND_DATA_ON_REQUEST)
        );
    }

    private static function errorMessage(\Throwable $throwable, ?array $additionalData = null): array
    {
        $throwableLog = AppHelper::throwableLog($throwable, false);
        if (!$additionalData) {
            return $throwableLog;
        }
        $throwableLog['additionalData'] = $additionalData;
        return $throwableLog;
    }
}

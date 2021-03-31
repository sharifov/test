<?php

namespace webapi\modules\v1\controllers;

use common\models\ApiLog;
use common\models\Payment;
use modules\flight\models\FlightQuote;
use modules\flight\src\exceptions\FlightCodeException;
use modules\flight\src\forms\api\FlightUpdateRequestApiForm;
use modules\flight\src\forms\TicketFlightsForm;
use modules\flight\src\repositories\flightQuoteRepository\FlightQuoteRepository;
use modules\flight\src\services\api\FlightUpdateRequestApiService;
use modules\flight\src\services\api\TicketIssueCheckDataService;
use modules\flight\src\services\api\TicketIssueProcessingDataService;
use modules\order\src\payment\PaymentRepository;
use modules\order\src\processManager\clickToBook\events\FlightProductProcessedErrorEvent;
use modules\order\src\processManager\clickToBook\events\FlightProductProcessedSuccessEvent;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\exceptions\ProductCodeException;
use sales\helpers\app\AppHelper;
use sales\repositories\product\ProductQuoteRepository;
use sales\services\TransactionManager;
use webapi\src\ApiCodeException;
use webapi\src\Messages;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\DataMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\Response;
use webapi\src\response\SuccessResponse;
use Yii;
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
 */
class FlightController extends ApiBaseController
{
    private FlightQuoteRepository $flightQuoteRepository;
    private ProductQuoteRepository $productQuoteRepository;
    private TransactionManager $transactionManager;
    private PaymentRepository $paymentRepository;
    private TicketIssueProcessingDataService $ticketIssueProcessingDataService;
    private EventDispatcher $eventDispatcher;

    public function __construct(
        $id,
        $module,
        FlightQuoteRepository $flightQuoteRepository,
        ProductQuoteRepository $productQuoteRepository,
        TransactionManager $transactionManager,
        PaymentRepository $paymentRepository,
        TicketIssueProcessingDataService $ticketIssueProcessingDataService,
        EventDispatcher $eventDispatcher,
        $config = []
    ) {
        $this->flightQuoteRepository = $flightQuoteRepository;
        $this->productQuoteRepository = $productQuoteRepository;
        $this->transactionManager = $transactionManager;
        $this->paymentRepository = $paymentRepository;
        $this->ticketIssueProcessingDataService = $ticketIssueProcessingDataService;
        $this->eventDispatcher = $eventDispatcher;

        parent::__construct($id, $module, $config);
    }

    /**
     * @api {post} /v1/flight/ticket Flight Ticket
     * @apiVersion 0.1.0
     * @apiName Flight Ticket
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
     *
     * @apiParamExample {json} Request-Example:
     *
     *   {
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
            "trips": []
        }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     * {
     *      "status": 200,
     *      "message": "OK",
     *      "data": {
     *          "resultMessage": "ProductQuote (exampleGID) changed status from (Pending) to (Error)"
     *      }
     * }
     *
     * @apiErrorExample {json} Error-Response (422):
     *
     * HTTP/1.1 200 OK
     *
     * {
     *     "status": 422,
     *     "message": "Validation error",
     *     "errors": {
     *         "uniqueId": [
     *             "UniqueId cannot be blank."
     *        ]
     *     },
     *     "code": "15801"
     * }
     *
     * @apiErrorExample {json} Error-Response (404):
     *
     * HTTP/1.1 200 OK
     *
     * {
     *     "status": 404,
     *     "message": "FlightQuote not found",
     *     "code": "15300",
     *     "errors": []
     * }
     */
    public function actionTicket()
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

        $form = new TicketFlightsForm();
        $post = Yii::$app->request->post();
        $flights = \Yii::$app->request->post('flights');
        if (!$flights) {
            return $this->endApiLog($apiLog, new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('Flights is not provided'),
                new CodeMessage(ApiCodeException::EVENT_OR_DATA_IS_NOT_PROVIDED)
            ));
        }

        $resultMessage = [];
        foreach ($flights as $key => $flight) {
            if (!$form->load($flight)) {
                return $this->endApiLog($apiLog, new ErrorResponse(
                    new StatusCodeMessage(400),
                    new MessageMessage(Messages::LOAD_DATA_ERROR),
                    new ErrorsMessage('Not found flights data on request'),
                    new CodeMessage(FlightCodeException::API_TICKET_FLIGHT_NOT_FOUND_DATA_ON_REQUEST)
                ));
            }
            if (!$form->validate()) {
                return $this->endApiLog($apiLog, new ErrorResponse(
                    new MessageMessage(Messages::VALIDATION_ERROR),
                    new ErrorsMessage($form->getErrors()),
                    new CodeMessage(FlightCodeException::API_TICKET_FLIGHT_VALIDATE)
                ));
            }
            if (!$flightQuote = FlightQuote::find()->where(['fq_flight_request_uid' => $form->uniqueId])->orderBy(['fq_id' => SORT_DESC])->one()) {
                return $this->endApiLog($apiLog, new ErrorResponse(
                    new StatusCodeMessage(404),
                    new MessageMessage('FlightQuote not found by uid (' . $form->uniqueId . ')'),
                    new CodeMessage(FlightCodeException::FLIGHT_QUOTE_NOT_FOUND)
                ));
            }

            /** @var FlightQuote $flightQuote */
            if (!$productQuote = $flightQuote->fqProductQuote) {
                return $this->endApiLog($apiLog, new ErrorResponse(
                    new StatusCodeMessage(404),
                    new MessageMessage('ProductQuote not found by FlightQuote (' . $form->uniqueId . ')'),
                    new CodeMessage(ProductCodeException::PRODUCT_QUOTE_OPTION_NOT_FOUND)
                ));
            }

            try {
                $oldStatusName = ProductQuoteStatus::getName($productQuote->pq_status_id);

                $productQuote = $this->transactionManager->wrap(function () use ($form, $post, $productQuote, $flightQuote) {
                    if ($form->status === FlightUpdateRequestApiService::SUCCESS_STATUS) {
                        $productQuote->booked();
                        $flightQuote->fq_ticket_json = $post;
                        $this->flightQuoteRepository->save($flightQuote);
                    } else {
                        $productQuote->error();
                    }
                    $this->productQuoteRepository->save($productQuote);
                    return $productQuote;
                });

                $newStatusName = ProductQuoteStatus::getName($productQuote->pq_status_id);
                $resultMessage[] = 'ProductQuote (' . $productQuote->pq_gid .
                    ') changed status from (' . $oldStatusName . ') to (' . $newStatusName . ')';
            } catch (\Throwable $throwable) {
                Yii::error(AppHelper::throwableLog($throwable), 'FlightController:actionUpdate:TransactionFailed');
                return $this->endApiLog($apiLog, new ErrorResponse(
                    new StatusCodeMessage(400),
                    new MessageMessage('Transaction failed. ProductQuote (' . $productQuote->pq_gid . ') not saved.'),
                    new CodeMessage(ProductCodeException::PRODUCT_QUOTE_SAVE)
                ));
            }
        }

        return $this->endApiLog($apiLog, new SuccessResponse(
            new StatusCodeMessage(200),
            new MessageMessage('OK'),
            new DataMessage([
                'resultMessage' => implode(', ', $resultMessage),
            ])
        ));
    }

    /**
     * @api {post} /v1/flight/update Flight Update
     * @apiVersion 0.1.0
     * @apiName Flight Update
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
     * @apiParamExample {json} Request-Example:
     *
     *   {
            "type": "ticket_issue", // flight_replace, flight_fail
            "orderUid": "order uid example",
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
            "payments":[ // if type ticket_issue
                {
                    "pay_amount":154.21,
                    "pay_currency":"usd",
                    "pay_code":"ch_1IYvYZFhXDZuLIpUisShKSRP",
                    "pay_method_key":"card",
                    "pay_date":"2021-03-25",
                    "pay_description": "example description"
                },
                {
                    "pay_amount":54.35,
                    "pay_currency":"eur",
                    "pay_code":"transactionIdcode",
                    "pay_method_key":"card",
                    "pay_date":"2021-03-29",
                    "pay_description": "example description"
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
     *          "resultMessage": "ProductQuote (exampleGID) changed status from (Pending) to (Error)"
     *      }
     * }
     *
     * @apiErrorExample {json} Error-Response (422):
     * HTTP/1.1 200 OK
     * {
     *     "status": 422,
     *     "message": "Validation error",
     *     "errors": {
     *         "status": [
     *             "status cannot be blank."
     *        ]
     *     },
     *     "code": "15801"
     * }
     *
     * @apiErrorExample {json} Error-Response (404):
     * HTTP/1.1 200 OK
     * {
     *     "status": 404,
     *     "message": "FlightQuote not found",
     *     "code": "15300",
     *     "errors": []
     * }
     */
    public function actionUpdate()
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

        $flightUpdateApiForm = new FlightUpdateRequestApiForm();
        $post = Yii::$app->request->post();
        if (!$flightUpdateApiForm->load($post)) {
            return $this->endApiLog($apiLog, new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found data on request'),
                new CodeMessage(FlightCodeException::API_TICKET_FLIGHT_NOT_FOUND_DATA_ON_REQUEST)
            ));
        }
        if (!$flightUpdateApiForm->validate()) {
            return $this->endApiLog($apiLog, new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($flightUpdateApiForm->getErrors()),
                new CodeMessage(FlightCodeException::API_TICKET_FLIGHT_VALIDATE)
            ));
        }

        if ($flightUpdateApiForm->type === FlightUpdateRequestApiService::TYPE_TICKET_ISSUE) {
            try {
                TicketIssueCheckDataService::checkFlights($flightUpdateApiForm->flights);
                TicketIssueCheckDataService::checkPayments($flightUpdateApiForm->payments);

                $this->transactionManager->wrap(function () use ($flightUpdateApiForm) {
                    $this->ticketIssueProcessingDataService->processingQuote($flightUpdateApiForm);
                    $this->ticketIssueProcessingDataService->processingPayment($flightUpdateApiForm);
                    $this->eventDispatcher->dispatch(new FlightProductProcessedSuccessEvent($flightUpdateApiForm->order->or_id));
                });
                return $this->endApiLog(
                    $apiLog,
                    self::generateSuccessResponse($flightUpdateApiForm->type)
                );
            } catch (\Throwable $throwable) {
                return $this->endApiLog($apiLog, new ErrorResponse(
                    new StatusCodeMessage(400),
                    new MessageMessage($throwable->getMessage()),
                    new CodeMessage(FlightCodeException::API_TICKET_ISSUE_FAILED)
                ));
            }
        } elseif ($flightUpdateApiForm->type === FlightUpdateRequestApiService::TYPE_FLIGHT_FAIL) {
            try {
                $this->transactionManager->wrap(function () use ($flightUpdateApiForm) {
                    $this->ticketIssueProcessingDataService->failQuote($flightUpdateApiForm->order);
                    $this->eventDispatcher->dispatch(new FlightProductProcessedErrorEvent($flightUpdateApiForm->order->or_id));
                });
                return $this->endApiLog(
                    $apiLog,
                    self::generateSuccessResponse($flightUpdateApiForm->type)
                );
            } catch (\Throwable $throwable) {
                return $this->endApiLog($apiLog, new ErrorResponse(
                    new StatusCodeMessage(400),
                    new MessageMessage($throwable->getMessage()),
                    new CodeMessage(FlightCodeException::API_FLIGHT_FAIL_FAILED)
                ));
            }
        } elseif ($flightUpdateApiForm->type === FlightUpdateRequestApiService::TYPE_FLIGHT_REPLACE) {
            try {
                // TODO::
                $x = true;
                return $this->endApiLog(
                    $apiLog,
                    self::generateSuccessResponse($flightUpdateApiForm->type)
                );
            } catch (\Throwable $throwable) {
                return $this->endApiLog($apiLog, new ErrorResponse(
                    new StatusCodeMessage(400),
                    new MessageMessage($throwable->getMessage()),
                    new CodeMessage(FlightCodeException::API_FLIGHT_REPLACE_FAILED)
                ));
            }
        } else {
            return $this->endApiLog($apiLog, new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('Undefined type (' . $flightUpdateApiForm->type . ')'),
                new CodeMessage(FlightCodeException::API_TICKET_FLIGHT_NOT_FOUND_DATA_ON_REQUEST)
            ));
        }
    }

    private static function generateSuccessResponse(string $type): SuccessResponse
    {
        return new SuccessResponse(
            new StatusCodeMessage(200),
            new MessageMessage('OK'),
            new DataMessage([
                'type' => $type,
                'resultMessage' => 'Flight update request successfully processed',
            ])
        );
    }

    private function endApiLog(ApiLog $apiLog, Response $response): Response
    {
        $apiLog->endApiLog(ArrayHelper::toArray($response));
        return $response;
    }
}

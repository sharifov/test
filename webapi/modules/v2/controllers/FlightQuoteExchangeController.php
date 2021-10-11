<?php

namespace webapi\modules\v2\controllers;

use common\components\jobs\VoluntaryExchangeCreateJob;
use modules\flight\models\FlightRequest;
use modules\flight\src\repositories\flightRequest\FlightRequestRepository;
use modules\flight\src\useCases\voluntaryExchange\service\VoluntaryExchangeObjectCollection;
use modules\flight\src\useCases\voluntaryExchangeCreate\form\VoluntaryExchangeCreateForm;
use modules\flight\src\useCases\voluntaryExchangeInfo\form\VoluntaryExchangeInfoForm;
use modules\flight\src\useCases\voluntaryExchangeInfo\service\VoluntaryExchangeInfoService;
use sales\helpers\app\AppHelper;
use sales\helpers\app\HttpStatusCodeHelper;
use webapi\src\ApiCodeException;
use webapi\src\logger\ApiLogger;
use webapi\src\Messages;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\DataMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\SuccessResponse;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class FlightQuoteExchangeController
 *
 * @property VoluntaryExchangeObjectCollection $objectCollection
 */
class FlightQuoteExchangeController extends BaseController
{
    private VoluntaryExchangeObjectCollection $objectCollection;

    /**
     * @param $id
     * @param $module
     * @param ApiLogger $logger
     * @param FlightRequestRepository $flightRequestRepository
     * @param VoluntaryExchangeObjectCollection $voluntaryExchangeObjectCollection
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        ApiLogger $logger,
        VoluntaryExchangeObjectCollection $voluntaryExchangeObjectCollection,
        $config = []
    ) {
        $this->objectCollection = $voluntaryExchangeObjectCollection;
        parent::__construct($id, $module, $logger, $config);
    }

    /**
     * @api {post} /v2/flight-quote-exchange/create Voluntary Exchange Create
     * @apiVersion 0.2.0
     * @apiName Voluntary Exchange Create
     * @apiGroup Voluntary Exchange
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{7..10}}        booking_id                                      Booking ID

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
     * @apiParam {object}               payment_request                      Payment request
     * @apiParam {number}               payment_request.amount               Amount
     * @apiParam {string{3}}            payment_request.currency             Currency code
     * @apiParam {string{2}}            payment_request.method_key           Method key (for example "cc")
     * @apiParam {object}               payment_request.method_data          Method data
     * @apiParam {object}               payment_request.method_data.card     Card (for credit card)
     * @apiParam {string{50}}           payment_request.method_data.card.number          Number
     * @apiParam {string{50}}           [payment_request.method_data.card.holder_name]   Holder name
     * @apiParam {int}                  payment_request.method_data.card.exp_month       Month
     * @apiParam {int}                  payment_request.method_data.card.exp_year        Year
     * @apiParam {string{32}}           payment_request.method_data.card.cvv             CVV
     * @apiParam {object}                      [flight_product_quote]                          Flight quote
     * @apiParam {string{2}}                   flight_product_quote.gds                        Gds
     * @apiParam {string{10}}                  flight_product_quote.pcc                        pcc
     * @apiParam {string{50}}                  flight_product_quote.fareType                   ValidatingCarrier
     * @apiParam {object}                      flight_product_quote.trips                      Trips
     * @apiParam {int}                         [flight_product_quote.trips.duration]           Trip Duration
     * @apiParam {object}                      flight_product_quote.trips.segments                          Segments
     * @apiParam {string{format Y-m-d H:i}}    flight_product_quote.trips.segments.departureTime            DepartureTime
     * @apiParam {string{format Y-m-d H:i}}    flight_product_quote.trips.segments.arrivalTime              ArrivalTime
     * @apiParam {string{3}}                   flight_product_quote.trips.segments.departureAirportCode     Departure Airport Code IATA
     * @apiParam {string{3}}                   flight_product_quote.trips.segments.arrivalAirportCode       Arrival Airport Code IATA
     * @apiParam {int}                         [flight_product_quote.trips.segments.flightNumber]           Flight Number
     * @apiParam {string{1}}                   [flight_product_quote.trips.segments.bookingClass]           BookingClass
     * @apiParam {int}                         [flight_product_quote.trips.segments.duration]               Segment duration
     * @apiParam {string{3}}                   [flight_product_quote.trips.segments.departureAirportTerminal]     Departure Airport Terminal Code
     * @apiParam {string{3}}                   [flight_product_quote.trips.segments.arrivalAirportTerminal]       Arrival Airport Terminal Code
     * @apiParam {string{2}}                   [flight_product_quote.trips.segments.operatingAirline]       Operating Airline
     * @apiParam {string{2}}                   [flight_product_quote.trips.segments.marketingAirline]       Marketing Airline
     * @apiParam {string{30}}                  [flight_product_quote.trips.segments.airEquipType]          AirEquipType
     * @apiParam {string{3}}                   [flight_product_quote.trips.segments.marriageGroup]          MarriageGroup
     * @apiParam {int}                         [flight_product_quote.trips.segments.mileage]                Mileage
     * @apiParam {string{2}}                   [flight_product_quote.trips.segments.meal]                   Meal
     * @apiParam {string{50}}                  [flight_product_quote.trips.segments.fareCode]               Fare Code
     *
     * @apiParamExample {json} Request-Example:
         {
            "booking_id":"XXXYYYZ",
            "flight_quote":{
                "gds": "S",
                "pcc": "8KI0",
                "validatingCarrier": "PR",
                "fareType": "SR",
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
          },
            "payment":{
                "method_key":"cc",
                "method_data":{
                    "card":{
                        "number":"4111555577778888",
                        "holder_name":"John Doe",
                        "exp_month":10,
                        "exp_year":2022,
                        "cvv":"097"
                    }
                },
                "amount": 29.95,
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
                    "TODO": "todo::"
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
            $bookingId = $voluntaryExchangeCreateForm->booking_id;
            if ($productQuoteChange = VoluntaryExchangeInfoService::getLastProductQuoteChange($bookingId)) {
                throw new \RuntimeException('VoluntaryExchange by BookingID(' . $bookingId . ') already processed');
            }

            $flightRequest = FlightRequest::create(
                $bookingId,
                FlightRequest::TYPE_VOLUNTARY_EXCHANGE_CREATE,
                $post,
                $project->id,
                $this->auth->getId()
            );
            $flightRequest = $this->objectCollection->getFlightRequestRepository()->save($flightRequest);

            $job = new VoluntaryExchangeCreateJob();
            $job->flight_request_id = $flightRequest->fr_id;
            $jobId = Yii::$app->queue_job->priority(100)->push($job);

            $flightRequest->fr_job_id = $jobId;
            $this->objectCollection->getFlightRequestRepository()->save($flightRequest);

            $dataMessage['resultMessage'] = 'FlightRequest created';
            $dataMessage['flightRequestId'] = $flightRequest->fr_id;

            return new SuccessResponse(
                new DataMessage($dataMessage),
                new CodeMessage(ApiCodeException::SUCCESS)
            );
        } catch (\RuntimeException | \DomainException $throwable) {
            \Yii::warning(
                ArrayHelper::merge(AppHelper::throwableLog($throwable), $post),
                'FlightQuoteExchangeController:actionInfo:Warning'
            );
            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage($throwable->getCode())
            );
        } catch (\Throwable $throwable) {
            \Yii::error(
                ArrayHelper::merge(AppHelper::throwableLog($throwable), $post),
                'FlightQuoteExchangeController:actionInfo:Throwable'
            );
            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::INTERNAL_SERVER_ERROR),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage($throwable->getCode())
            );
        }
    }

    /**
     * @api {post} /v2/flight-quote-exchange/info Voluntary Exchange Info
     * @apiVersion 0.2.0
     * @apiName Voluntary Exchange Info
     * @apiGroup Voluntary Exchange
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
                new DataMessage([
                    'productQuoteChange' => $productQuoteChange
                        ->setFields(VoluntaryExchangeInfoService::apiDataMapper($productQuoteChange))
                        ->toArray(),
                ]),
                new CodeMessage(ApiCodeException::SUCCESS)
            );
        } catch (\RuntimeException | \DomainException $throwable) {
            \Yii::warning(
                ArrayHelper::merge(AppHelper::throwableLog($throwable), $post),
                'FlightQuoteExchangeController:actionInfo:Warning'
            );
            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::UNPROCESSABLE_ENTITY),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage($throwable->getCode())
            );
        } catch (\Throwable $throwable) {
            \Yii::error(
                ArrayHelper::merge(AppHelper::throwableLog($throwable), $post),
                'FlightQuoteExchangeController:actionInfo:Throwable'
            );
            return new ErrorResponse(
                new StatusCodeMessage(HttpStatusCodeHelper::INTERNAL_SERVER_ERROR),
                new ErrorsMessage($throwable->getMessage()),
                new CodeMessage($throwable->getCode())
            );
        }
    }
}

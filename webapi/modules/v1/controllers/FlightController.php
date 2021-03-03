<?php

namespace webapi\modules\v1\controllers;

use common\models\ApiLog;
use modules\flight\models\FlightQuote;
use modules\flight\src\exceptions\FlightCodeException;
use modules\flight\src\forms\TicketFlightsForm;
use modules\flight\src\jobs\FlightQuotePdfJob;
use modules\flight\src\repositories\flightQuoteRepository\FlightQuoteRepository;
use modules\flight\src\services\flightQuote\FlightQuotePdfService;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use sales\helpers\app\AppHelper;
use sales\repositories\product\ProductQuoteRepository;
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

/**
 * Class FlightController
 */
class FlightController extends ApiBaseController
{
    /**
     * @api {get} /v1/flight/ticket Flight Ticket
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
     *          "flightRequestUid": "OE96040",
     *          "flightQuoteId": 12345,
     *          "productQuoteGid": "58a60dfd87b1728f422e1871fd302fcf",
     *          "statusMessage": "ProductQuote changed status from (In progress) to (Error)"
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

        if (!$form->load($flights[0])) {
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

        if (!$flightQuote = FlightQuote::findOne(['fq_flight_request_uid' => $form->uniqueId])) {
            return $this->endApiLog($apiLog, new ErrorResponse(
                new StatusCodeMessage(404),
                new MessageMessage('FlightQuote not found'),
                new CodeMessage(FlightCodeException::FLIGHT_QUOTE_NOT_FOUND)
            ));
        }

        $flightQuoteRepository = Yii::createObject(FlightQuoteRepository::class);
        $productQuoteRepository = Yii::createObject(ProductQuoteRepository::class);
        $productQuote = $flightQuote->fqProductQuote;
        $oldStatus = ProductQuoteStatus::getName($productQuote->pq_status_id);

        if ($form->status === $form::SUCCESS_STATUS) {
            $productQuote->booked();

            $flightQuotePdfJob = new FlightQuotePdfJob();
            $flightQuotePdfJob->flightQuoteId = $flightQuote->fq_id;

            Yii::$app->queue_job->priority(10)->push($flightQuotePdfJob);
        } else {
            $productQuote->error();
        }
        $productQuoteRepository->save($productQuote);

        $flightQuote->fq_ticket_json = $post;
        $flightQuoteRepository->save($flightQuote);

        $newStatus = ProductQuoteStatus::getName($productQuote->pq_status_id);
        $statusMessage = 'ProductQuote changed status from (' . $oldStatus . ') to (' . $newStatus . ')';

        return $this->endApiLog($apiLog, new SuccessResponse(
            new StatusCodeMessage(200),
            new MessageMessage('OK'),
            new DataMessage([
                'flightQuoteUid' => $flightQuote->fq_uid,
                'flightQuoteId' => $flightQuote->fq_id,
                'productQuoteGid' => $productQuote->pq_gid,
                'statusMessage' => $statusMessage,
            ])
        ));
    }

    private function endApiLog(ApiLog $apiLog, Response $response): Response
    {
        $apiLog->endApiLog(ArrayHelper::toArray($response));
        return $response;
    }
}

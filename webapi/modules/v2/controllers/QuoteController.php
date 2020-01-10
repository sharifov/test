<?php

namespace webapi\modules\v2\controllers;

use common\models\Lead;
use common\models\Notifications;
use common\models\Quote;
use common\models\UserProjectParams;
use Yii;
use yii\helpers\Html;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;


class QuoteController extends ApiBaseController
{

    /**
     *
     * @api {post} /v2/quote/get-info Get Quote
     * @apiVersion 0.2.0
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
     * @apiSuccess {object} result Result of itinerary and pricing
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
     * "action": "v2/quote/get-info",
     *
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *
     *
     * {"status": "Success",
    "result": {
        "prices": {
            "totalPrice": 2056.98,
            "totalTax": 1058.98,
            "isCk": true
        },
        "passengers": {
            "ADT": {
                "cnt": 2,
                "price": 1028.49,
                "tax": 529.49,
                "baseFare": 499
            },
            "INF": {
                "cnt": 1,
                "price": 0,
                "tax": 0,
                "baseFare": 0
            }
        },
        "trips": [
            {
                "tripId": 1,
                "segments": [
                    {
                        "segmentId": 1,
                        "departureTime": "2019-12-06 16:20",
                        "arrivalTime": "2019-12-06 17:57",
                        "stop": 0,
                        "stops": null,
                        "flightNumber": "7312",
                        "bookingClass": "T",
                        "duration": 97,
                        "departureAirportCode": "IND",
                        "departureAirportTerminal": "",
                        "arrivalAirportCode": "YYZ",
                        "arrivalAirportTerminal": "",
                        "operatingAirline": "AC",
                        "airEquipType": null,
                        "marketingAirline": "AC",
                        "cabin": "Y",
                        "ticket_id": 1,
                        "baggage": {
                            "": {
                                "allowPieces": 2,
                                "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
                                "allowMaxWeight": "UP TO 50 POUNDS/23 KILOGRAMS",
                                "charge": {
                                    "price": 100,
                                    "maxWeight": "UP TO 50 POUNDS/23 KILOG RAMS",
                                    "maxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
                                    "firstPiece": 1,
                                    "lastPiece": 1
                                }
                            }
                        }
                    },
                    {
                        "segmentId": 2,
                        "departureTime": "2019-12-06 20:45",
                        "arrivalTime": "2019-12-07 09:55",
                        "stop": 0,
                        "stops": null,
                        "flightNumber": "880",
                        "bookingClass": "T",
                        "duration": 430,
                        "departureAirportCode": "YYZ",
                        "departureAirportTerminal": "",
                        "arrivalAirportCode": "CDG",
                        "arrivalAirportTerminal": "",
                        "operatingAirline": "AC",
                        "airEquipType": null,
                        "marketingAirline": "AC",
                        "cabin": "Y",
                        "ticket_id": 2,
                        "baggage": {
                            "": {
                                "allowPieces": 2,
                                "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
                                "allowMaxWeight": "UP TO 50 POUNDS/23 KILOGRAMS",
                                "charge": {
                                    "price": 100,
                                    "maxWeight": "UP TO 50 POUNDS/23 KILOG RAMS",
                                    "maxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
                                    "firstPiece": 1,
                                    "lastPiece": 1
                                }
                            }
                        }
                    },
                    {
                        "segmentId": 3,
                        "departureTime": "2019-12-07 13:40",
                        "arrivalTime": "2019-12-07 19:05",
                        "stop": 0,
                        "stops": null,
                        "flightNumber": "6692",
                        "bookingClass": "T",
                        "duration": 265,
                        "departureAirportCode": "CDG",
                        "departureAirportTerminal": "",
                        "arrivalAirportCode": "IST",
                        "arrivalAirportTerminal": "",
                        "operatingAirline": "AC",
                        "airEquipType": null,
                        "marketingAirline": "AC",
                        "cabin": "Y",
                        "ticket_id": 2,
                        "baggage": {
                            "": {
                                "allowPieces": 2,
                                "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
                                "allowMaxWeight": "UP TO 50 POUNDS/23 KILOGRAMS",
                                "charge": {
                                    "price": 100,
                                    "maxWeight": "UP TO 50 POUNDS/23 KILOG RAMS",
                                    "maxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
                                    "firstPiece": 1,
                                    "lastPiece": 1
                                }
                            }
                        }
                    }
                ],
                "duration": 1185
            },
            {
                "tripId": 2,
                "segments": [
                    {
                        "segmentId": 1,
                        "departureTime": "2019-12-25 09:15",
                        "arrivalTime": "2019-12-25 10:35",
                        "stop": 0,
                        "stops": null,
                        "flightNumber": "6681",
                        "bookingClass": "T",
                        "duration": 140,
                        "departureAirportCode": "IST",
                        "departureAirportTerminal": "",
                        "arrivalAirportCode": "GVA",
                        "arrivalAirportTerminal": "",
                        "operatingAirline": "AC",
                        "airEquipType": null,
                        "marketingAirline": "AC",
                        "cabin": "Y",
                        "ticket_id": 1,
                        "baggage": {
                            "": {
                                "allowPieces": 1,
                                "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS ‡",
                                "allowMaxWeight": "UP TO 50 POUNDS/23 ‡ MD« KILOGRAMS",
                                "charge": {
                                    "price": 100,
                                    "maxWeight": "UP TO 50 POUNDS/23 KILOG RAMS",
                                    "maxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS CARRY ON ALLOWANCE INDYYZ YYZCDG GVAYYZ YYZIND",
                                    "firstPiece": 1,
                                    "lastPiece": 1
                                }
                            }
                        }
                    },
                    {
                        "segmentId": 2,
                        "departureTime": "2019-12-25 12:00",
                        "arrivalTime": "2019-12-25 17:34",
                        "stop": 0,
                        "stops": null,
                        "flightNumber": "835",
                        "bookingClass": "T",
                        "duration": 694,
                        "departureAirportCode": "GVA",
                        "departureAirportTerminal": "",
                        "arrivalAirportCode": "YYZ",
                        "arrivalAirportTerminal": "",
                        "operatingAirline": "AC",
                        "airEquipType": null,
                        "marketingAirline": "AC",
                        "cabin": "Y",
                        "ticket_id": 2,
                        "baggage": {
                            "": {
                                "allowPieces": 1,
                                "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS ‡",
                                "allowMaxWeight": "UP TO 50 POUNDS/23 ‡ MD« KILOGRAMS",
                                "charge": {
                                    "price": 100,
                                    "maxWeight": "UP TO 50 POUNDS/23 KILOG RAMS",
                                    "maxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS CARRY ON ALLOWANCE INDYYZ YYZCDG GVAYYZ YYZIND",
                                    "firstPiece": 1,
                                    "lastPiece": 1
                                }
                            }
                        }
                    },
                    {
                        "segmentId": 3,
                        "departureTime": "2019-12-25 20:55",
                        "arrivalTime": "2019-12-25 22:37",
                        "stop": 0,
                        "stops": null,
                        "flightNumber": "7313",
                        "bookingClass": "T",
                        "duration": 102,
                        "departureAirportCode": "YYZ",
                        "departureAirportTerminal": "",
                        "arrivalAirportCode": "IND",
                        "arrivalAirportTerminal": "",
                        "operatingAirline": "AC",
                        "airEquipType": null,
                        "marketingAirline": "AC",
                        "cabin": "Y",
                        "ticket_id": 2,
                        "baggage": {
                            "": {
                                "allowPieces": 1,
                                "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS ‡",
                                "allowMaxWeight": "UP TO 50 POUNDS/23 ‡ MD« KILOGRAMS",
                                "charge": {
                                    "price": 100,
                                    "maxWeight": "UP TO 50 POUNDS/23 KILOG RAMS",
                                    "maxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS CARRY ON ALLOWANCE INDYYZ YYZCDG GVAYYZ YYZIND",
                                    "firstPiece": 1,
                                    "lastPiece": 1
                                }
                            }
                        }
                    }
                ],
                "duration": 1222
            }
        ],
        "validatingCarrier": "AC",
        "fareType": "PUB",
        "tripType": "RT",
        "currency": "USD",
        "currencyRate": 1
    },
    "errors": [],
    "uid": "5cb97d1c78486",
    "lead_id": 92322,
    "lead_uid": "5cb8735a502f5",
    "client_id": 1034,
    "lead_delayed_charge": 0,
    "lead_status": null,
    "booked_quote_uid": null,
    "agentName": "admin",
    "agentEmail": "admin@wowfare.com",
    "agentDirectLine": "",
    "generalEmail": "info@wowfare.com",
    "generalDirectLine": "+37379731662",
    "quote": {
        "id": 382366,
        "uid": "5d43e1ec36372",
        "lead_id": 178363,
        "employee_id": 167,
        "record_locator": "",
        "pcc": "DFWG32100",
        "cabin": "E",
        "gds": "A",
        "trip_type": "OW",
        "main_airline_code": "SU",
        "reservation_dump": "1  SU1845T  22AUG  KIVSVO    255A    555A  TH",
        "status": 5,
        "check_payment": 1,
        "fare_type": "PUB",
        "created": "2019-08-02 07:10:36",
        "updated": "2019-08-05 08:58:18",
        "created_by_seller": 1,
        "employee_name": "alex.connor2",
        "last_ticket_date": "2019-08-09 00:00:00",
        "service_fee_percent": null,
        "pricing_info": null,
        "alternative": 1,
        "tickets": "[{\"key\":\"02_QVdBUlFBKlkxMDAwL05ZQ01BRDIwMTktMDgtMjYvTUFETllDMjAxOS0wOS0wNipVQX4jVUE1MSNVQTUw\",\"routingId\":0,\"prices\":{\"lastTicketDate\":\"2019-08-11\",\"totalPrice\":392.73,\"totalTax\":272.73,\"markup\":50,\"markupId\":0,\"isCk\":false,\"oMarkup\":{\"amount\":50,\"currency\":\"USD\"}},\"passengers\":{\"ADT\":{\"codeAs\":\"JWZ\",\"cnt\":1,\"price\":392.73,\"tax\":272.73,\"baseFare\":120,\"pubBaseFare\":120,\"baseTax\":222.73,\"markup\":50,\"refundPenalty\":\"\",\"changePenalty\":\"Percentage: 100.00%\",\"endorsementPenalty\":\"\",\"publishFare\":false,\"fareDescription\":\"\",\"oBaseFare\":{\"amount\":120,\"currency\":\"USD\"},\"oBaseTax\":{\"amount\":222.73,\"currency\":\"USD\"},\"oMarkup\":{\"amount\":50,\"currency\":\"USD\"}}},\"maxSeats\":0,\"validatingCarrier\":\"UA\",\"gds\":\"T\",\"pcc\":\"E9V\",\"fareType\":\"SR\",\"tripType\":\"RT\",\"cabin\":\"Y\",\"currency\":\"USD\",\"trips\":[{\"tripId\":1,\"segmentIds\":[1]},{\"tripId\":2,\"segmentIds\":[3]}]},{\"key\":\"02_QVdBUlFBKlkxMDAwL01BRFZJRTIwMTktMDgtMjcvVklFTUFEMjAxOS0wOS0wNSpMWH4jTFgyMDI3I0xYMzU2OCNMWDM1NjMjTFgyMDQ4\",\"routingId\":0,\"prices\":{\"lastTicketDate\":\"2019-08-09\",\"totalPrice\":305.3,\"totalTax\":184.3,\"markup\":50,\"markupId\":0,\"isCk\":false,\"oMarkup\":{\"amount\":50,\"currency\":\"USD\"}},\"passengers\":{\"ADT\":{\"codeAs\":\"ADT\",\"cnt\":1,\"price\":305.3,\"tax\":184.3,\"baseFare\":121,\"pubBaseFare\":121,\"baseTax\":134.3,\"markup\":50,\"refundPenalty\":\"Percentage: 100.00%\",\"changePenalty\":\"Percentage: 100.00%\",\"endorsementPenalty\":\"\",\"publishFare\":false,\"fareDescription\":\"\",\"oBaseFare\":{\"amount\":121,\"currency\":\"USD\"},\"oBaseTax\":{\"amount\":134.3,\"currency\":\"USD\"},\"oMarkup\":{\"amount\":50,\"currency\":\"USD\"}}},\"maxSeats\":0,\"validatingCarrier\":\"LX\",\"gds\":\"T\",\"pcc\":\"E9V\",\"fareType\":\"PUB\",\"tripType\":\"RT\",\"cabin\":\"Y\",\"currency\":\"USD\",\"trips\":[{\"tripId\":1,\"segmentIds\":[2,3]},{\"tripId\":2,\"segmentIds\":[1,2]}]}]",
        "origin_search_data": "{\"key\":\"01_U0FMMTAxKlkxMDAwL0pGS0ZSQTIwMTktMTEtMjEqUk9+I1FSNzA0I1FSMjI3I1JPMjk4I1JPMzAxOjNkMjBiYzI5LWIzMmItNGJhOC05OTljLTQ4ZTFlYWI1NGU1Ng==\",\"routingId\":306,\"gdsOfferId\":\"3d20bc29-b32b-4ba8-999c-48e1eab54e56\",\"prices\":{\"lastTicketDate\":\"2019-11-23\",\"totalPrice\":670.35,\"totalTax\":367.35,\"markup\":100,\"markupId\":0,\"isCk\":false,\"oMarkup\":{\"amount\":100,\"currency\":\"USD\"}},\"passengers\":{\"ADT\":{\"codeAs\":\"JWZ\",\"cnt\":1,\"price\":670.35,\"tax\":367.35,\"baseFare\":303,\"pubBaseFare\":303,\"baseTax\":267.35,\"markup\":100,\"refundPenalty\":\"Amount: USD375.00 Percentage: 100.00%\",\"changePenalty\":\"Amount: USD260.00 Percentage: 100.00%\",\"endorsementPenalty\":\" \",\"publishFare\":false,\"fareDescription\":\"\",\"oBaseFare\":{\"amount\":303,\"currency\":\"USD\"},\"oPubBaseFare\":{\"amount\":303,\"currency\":\"USD\"},\"oBaseTax\":{\"amount\":267.35,\"currency\":\"USD\"},\"oMarkup\":{\"amount\":100,\"currency\":\"USD\"}}},\"trips\":[{\"tripId\":1,\"segments\":[{\"segmentId\":1,\"departureTime\":\"2019-11-21 09:45\",\"arrivalTime\":\"2019-11-22 06:00\",\"stop\":0,\"stops\":[],\"flightNumber\":\"704\",\"bookingClass\":\"N\",\"duration\":735,\"departureAirportCode\":\"JFK\",\"departureAirportTerminal\":\"7\",\"arrivalAirportCode\":\"DOH\",\"arrivalAirportTerminal\":\"\",\"operatingAirline\":\"QR\",\"airEquipType\":\"351\",\"marketingAirline\":\"QR\",\"marriageGroup\":\"I\",\"mileage\":6689,\"cabin\":\"Y\",\"meal\":\"\",\"fareCode\":\"NLUSN1RO\",\"baggage\":{\"ADT\":{\"allowPieces\":2}},\"recheckBaggage\":false},{\"segmentId\":2,\"departureTime\":\"2019-11-22 07:10\",\"arrivalTime\":\"2019-11-22 11:25\",\"stop\":0,\"stops\":[],\"flightNumber\":\"227\",\"bookingClass\":\"N\",\"duration\":315,\"departureAirportCode\":\"DOH\",\"departureAirportTerminal\":\"\",\"arrivalAirportCode\":\"SOF\",\"arrivalAirportTerminal\":\"2\",\"operatingAirline\":\"QR\",\"airEquipType\":\"320\",\"marketingAirline\":\"QR\",\"marriageGroup\":\"O\",\"mileage\":1999,\"cabin\":\"Y\",\"meal\":\"\",\"fareCode\":\"NLUSN1RO\",\"baggage\":{\"ADT\":{\"allowPieces\":2}},\"recheckBaggage\":false},{\"segmentId\":3,\"departureTime\":\"2019-11-22 19:45\",\"arrivalTime\":\"2019-11-22 20:50\",\"stop\":0,\"stops\":[],\"flightNumber\":\"298\",\"bookingClass\":\"T\",\"duration\":65,\"departureAirportCode\":\"SOF\",\"departureAirportTerminal\":\"2\",\"arrivalAirportCode\":\"OTP\",\"arrivalAirportTerminal\":\"\",\"operatingAirline\":\"RO\",\"airEquipType\":\"AT7\",\"marketingAirline\":\"RO\",\"marriageGroup\":\"I\",\"mileage\":185,\"cabin\":\"Y\",\"meal\":\"\",\"fareCode\":\"TOWSVR\",\"baggage\":{\"ADT\":{\"allowPieces\":1}},\"recheckBaggage\":true},{\"segmentId\":4,\"departureTime\":\"2019-11-23 08:35\",\"arrivalTime\":\"2019-11-23 10:15\",\"stop\":0,\"stops\":[],\"flightNumber\":\"301\",\"bookingClass\":\"T\",\"duration\":160,\"departureAirportCode\":\"OTP\",\"departureAirportTerminal\":\"\",\"arrivalAirportCode\":\"FRA\",\"arrivalAirportTerminal\":\"2\",\"operatingAirline\":\"RO\",\"airEquipType\":\"73W\",\"marketingAirline\":\"RO\",\"marriageGroup\":\"O\",\"mileage\":903,\"cabin\":\"Y\",\"meal\":\"\",\"fareCode\":\"TOWSVR\",\"baggage\":{\"ADT\":{\"allowPieces\":1}},\"recheckBaggage\":false}],\"duration\":2550}],\"maxSeats\":0,\"validatingCarrier\":\"RO\",\"gds\":\"G\",\"pcc\":\"NA\",\"cons\":\"GIS\",\"fareType\":\"NA\",\"tripType\":\"OW\",\"cabin\":\"Y\",\"currency\":\"USD\",\"currencies\":[\"USD\"],\"currencyRates\":{\"USDUSD\":{\"from\":\"USD\",\"to\":\"USD\",\"rate\":1}},\"tickets\":[{\"key\":\"02_QVdBUlFBKlkxMDAwL0pGS1NPRjIwMTktMTEtMjEqUVJ+I1FSNzA0I1FSMjI3\",\"routingId\":0,\"prices\":{\"lastTicketDate\":\"2019-11-21\",\"totalPrice\":388.8,\"totalTax\":267.8,\"markup\":50,\"markupId\":0,\"isCk\":false,\"oMarkup\":{\"amount\":50,\"currency\":\"USD\"}},\"passengers\":{\"ADT\":{\"codeAs\":\"JWZ\",\"cnt\":1,\"price\":388.8,\"tax\":267.8,\"baseFare\":121,\"pubBaseFare\":121,\"baseTax\":217.8,\"markup\":50,\"refundPenalty\":\"Amount: USD375.00 \",\"changePenalty\":\"Amount: USD260.00\",\"endorsementPenalty\":\"\",\"publishFare\":false,\"fareDescription\":\"\",\"oBaseFare\":{\"amount\":121,\"currency\":\"USD\"},\"oBaseTax\":{\"amount\":217.8,\"currency\":\"USD\"},\"oMarkup\":{\"amount\":50,\"currency\":\"USD\"}}},\"maxSeats\":0,\"validatingCarrier\":\"QR\",\"gds\":\"T\",\"pcc\":\"E9V\",\"fareType\":\"SR\",\"tripType\":\"OW\",\"cabin\":\"Y\",\"currency\":\"USD\",\"trips\":[{\"tripId\":1,\"segmentIds\":[1,2]}]},{\"key\":\"01_QVdBUlFBKlkxMDAwL1NPRkZSQTIwMTktMTEtMjIqUk9+I1JPMjk4I1JPMzAx\",\"routingId\":0,\"prices\":{\"lastTicketDate\":\"2019-10-19\",\"totalPrice\":265.6,\"totalTax\":83.6,\"markup\":50,\"markupId\":0,\"isCk\":false,\"oMarkup\":{\"amount\":50,\"currency\":\"USD\"}},\"passengers\":{\"ADT\":{\"codeAs\":\"ADT\",\"cnt\":1,\"price\":265.6,\"tax\":83.6,\"baseFare\":182,\"pubBaseFare\":182,\"baseTax\":33.6,\"markup\":50,\"refundPenalty\":\"Percentage: 100.00%\",\"changePenalty\":\"Percentage: 100.00%\",\"endorsementPenalty\":\"\",\"publishFare\":false,\"fareDescription\":\"\",\"oBaseFare\":{\"amount\":182,\"currency\":\"USD\"},\"oBaseTax\":{\"amount\":33.6,\"currency\":\"USD\"},\"oMarkup\":{\"amount\":50,\"currency\":\"USD\"}}},\"maxSeats\":0,\"validatingCarrier\":\"RO\",\"gds\":\"T\",\"pcc\":\"E9V\",\"fareType\":\"PUB\",\"tripType\":\"OW\",\"cabin\":\"Y\",\"currency\":\"USD\",\"trips\":[{\"tripId\":1,\"segmentIds\":[3,4]}]}]}"
    },
    },
    "action": "v2/quote/get-info",
    "response_id": 298939,
    "request_dt": "2019-04-25 13:12:44",
    "response_dt": "2019-04-25 13:12:44"
}
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
            'result' => [],
            'errors' => []
        ];

        try {

            $response['status'] = $model->status != $model::STATUS_DECLINED ? 'Success' : 'Failed';

            $userProjectParams = UserProjectParams::findOne([
                'upp_user_id' => $model->lead->employee_id,
                'upp_project_id' => $model->lead->project_id
            ]);

            $response['uid'] = $uid;
            $response['lead_id'] = $model->lead->id;
            $response['lead_uid'] = $model->lead->uid;
            $response['client_id'] = $model->lead->client_id;
            $response['lead_delayed_charge'] = $model->lead->l_delayed_charge;
            $response['lead_status'] = null;
            $response['booked_quote_uid'] = null;

            if(in_array($model->lead->status,[Lead::STATUS_SOLD, Lead::STATUS_BOOKED])){
                $response['lead_status'] = $model->lead->status == Lead::STATUS_SOLD ? 'sold' : 'booked';
                $response['booked_quote_uid'] = $model->lead->getBookedQuoteUid();
            }

            $response['agentName'] = ($model->lead && $model->lead->employee) ? $model->lead->employee->username : null;
            $response['agentEmail'] = $userProjectParams ? $userProjectParams->upp_email : $model->lead->project->contactInfo->email;
            $response['agentDirectLine'] = $userProjectParams ? $userProjectParams->upp_tw_phone_number : sprintf('%s', $model->lead->project->contactInfo->phone);
            $response['generalEmail'] = $model->lead->project->contactInfo->email;
            $response['generalDirectLine'] = sprintf('%s', $model->lead->project->contactInfo->phone);


            $response['quote'] = $model->attributes;

            $response['result'] = [
                'prices' => [],
                'passengers' => [],
                'trips' => [],
                'validatingCarrier' => $model->main_airline_code,
                'fareType' => '',
                'tripType' => $model->trip_type,
                'currency' => 'USD',
                'currencyRate' => 1,
            ];





            $paxPriceData = $model->getQuotePricePassengersData();
            $response['result'] = array_merge($response['result'], $paxPriceData);
            $response['result']['trips'] = $model->getQuoteTripsData();

            if ((int)$model->status === Quote::STATUS_SEND) {
                $excludeIP = Quote::isExcludedIP($clientIP);
                if (!$excludeIP) {
                    $model->status = Quote::STATUS_OPENED;
                    if ($model->save()) {
                        $host = Yii::$app->params['url_address'];
                        $lead = $model->lead;
                        if ($lead) {
                            $project_name = $lead->project ? $lead->project->name : '';
                            $subject = 'Quote- ' . $model->uid . ' OPENED';
                            $message = 'Your Quote (UID: ' . $model->uid . ") has been OPENED by client! \r\nProject: " . Html::encode($project_name) . "! \r\n lead: " . $host . '/lead/view/' . $lead->gid;

                            if ($lead->employee_id) {
                                $isSend = Notifications::create($lead->employee_id, $subject, $message, Notifications::TYPE_INFO, true);
                                if ($isSend) {
                                    // Notifications::socket($lead->employee_id, null, 'getNewNotification', [], true);
                                    Notifications::sendSocket('getNewNotification', ['user_id' => $lead->employee_id]);
                                }
                            }
                        }
                    }
                    //exec(dirname(Yii::getAlias('@app')) . '/yii quote/send-opened-notification '.$uid.'  > /dev/null 2>&1 &');
                }
            }

        } catch (\Throwable $e) {

            Yii::error($e->getTraceAsString(), 'API:Quote:get:try');
            if (Yii::$app->request->get('debug')) $message = ($e->getTraceAsString());
            else $message = $e->getMessage() . ' (code:' . $e->getCode() . ', line: ' . $e->getLine() . ')';

            $response['error'] = $message;
            $response['errors'] = $message;
            $response['error_code'] = 30;
        }


        $responseData = $response;
        $responseData = $apiLog->endApiLog($responseData);

        if (isset($response['error']) && $response['error']) {
            $json = @json_encode($response['error']);
            if (isset($response['error_code']) && $response['error_code']) $error_code = $response['error_code'];
            else $error_code = 0;
            throw new UnprocessableEntityHttpException($json, $error_code);
        }

        return $responseData;
    }
}

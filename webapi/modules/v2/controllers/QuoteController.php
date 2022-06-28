<?php

namespace webapi\modules\v2\controllers;

use common\components\purifier\Purifier;
use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Lead;
use common\models\Notifications;
use common\models\Quote;
use common\models\QuoteCommunicationOpenLog;
use common\models\UserProjectParams;
use common\models\VisitorLog;
use frontend\helpers\JsonHelper;
use frontend\widgets\notification\NotificationMessage;
use src\helpers\app\AppHelper;
use src\model\leadData\services\LeadDataService;
use src\services\quote\quotePriceService\ClientQuotePriceService;
use webapi\src\behaviors\ApiUserProjectRelatedAccessBehavior;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;

class QuoteController extends ApiBaseController
{
    /**
     *
     * @return array
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     * @api {post} /v2/quote/get-info Get Quote
     * @apiVersion 0.2.0
     * @apiName GetQuote
     * @apiGroup Quotes v2
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
     * @apiSuccess {object} result Result of itinerary and pricing
     * @apiSuccess {array} errors    Errors
     * @apiSuccess {string} uid    Quote UID
     * @apiSuccess {integer} lead_id    Lead ID
     * @apiSuccess {string} lead_uid    Lead UID
     * @apiSuccess {integer} lead_type    <code>TYPE_ALTERNATIVE = 2, TYPE_FAILED_BOOK = 3</code>
     * @apiSuccess {string} agentName    Agent Name
     * @apiSuccess {string} agentEmail    Agent Email
     * @apiSuccess {string} agentDirectLine    Agent DirectLine
     * @apiSuccess {object}     [lead]                          Lead
     * @apiSuccess {string}     [lead.department_key]           Department key (For example: <code>sales,exchange,support,schedule_change,fraud_prevention,chat</code>)
     * @apiSuccess {integer}    [lead.type_create_id]           Type create id
     * @apiSuccess {string}     [lead.type_create_name]         Type Name
     * @apiSuccess {object}     [lead.lead_data]                Lead data
     * @apiSuccess {object}     [lead.additionalInformation]    Additional Information
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
     * HTTP/1.1 200 OK
     * {
     *   "status": "Success",
     *   "result": {
     *       "prices": {
     *           "totalPrice": 2056.98,
     *           "totalTax": 1058.98,
     *           "isCk": true
     *       },
     *       "passengers": {
     *           "ADT": {
     *               "cnt": 2,
     *               "price": 1028.49,
     *               "tax": 529.49,
     *               "baseFare": 499,
     *               "mark_up": 20,
     *               "extra_mark_up": 10,
     *               "baseTax": 499.49,
     *               "service_fee": 0
     *           },
     *           "INF": {
     *               "cnt": 1,
     *               "price": 0,
     *               "tax": 0,
     *               "baseFare": 0,
     *               "mark_up": 0,
     *               "extra_mark_up": 0,
     *               "baseTax": 0,
     *               "service_fee": 0
     *           }
     *       },
     *       "trips": [
     *           {
     *               "tripId": 1,
     *               "segments": [
     *                   {
     *                       "segmentId": 1,
     *                       "departureTime": "2019-12-06 16:20",
     *                       "arrivalTime": "2019-12-06 17:57",
     *                       "stop": 0,
     *                       "stops": null,
     *                       "flightNumber": "7312",
     *                       "bookingClass": "T",
     *                       "duration": 97,
     *                       "departureAirportCode": "IND",
     *                       "departureAirportTerminal": "",
     *                       "arrivalAirportCode": "YYZ",
     *                       "arrivalAirportTerminal": "",
     *                       "operatingAirline": "AC",
     *                       "airEquipType": null,
     *                       "marketingAirline": "AC",
     *                       "cabin": "Y",
     *                       "ticket_id": 1,
     *                       "baggage": {
     *                           "ADT": {
     *                               "allowPieces": 2,
     *                               "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
     *                               "allowMaxWeight": "UP TO 50 POUNDS/23 KILOGRAMS",
     *                               "charge": {
     *                                   "price": 100,
     *                                   "maxWeight": "UP TO 50 POUNDS/23 KILOG RAMS",
     *                                   "maxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
     *                                   "firstPiece": 1,
     *                                   "lastPiece": 1
     *                               }
     *                           }
     *                       }
     *                   },
     *                   {
     *                       "segmentId": 2,
     *                       "departureTime": "2019-12-06 20:45",
     *                       "arrivalTime": "2019-12-07 09:55",
     *                       "stop": 0,
     *                       "stops": null,
     *                       "flightNumber": "880",
     *                       "bookingClass": "T",
     *                       "duration": 430,
     *                       "departureAirportCode": "YYZ",
     *                       "departureAirportTerminal": "",
     *                       "arrivalAirportCode": "CDG",
     *                       "arrivalAirportTerminal": "",
     *                       "operatingAirline": "AC",
     *                       "airEquipType": null,
     *                       "marketingAirline": "AC",
     *                       "cabin": "Y",
     *                       "ticket_id": 2,
     *                       "baggage": {
     *                           "ADT": {
     *                               "allowPieces": 2,
     *                               "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
     *                               "allowMaxWeight": "UP TO 50 POUNDS/23 KILOGRAMS",
     *                               "charge": {
     *                                   "price": 100,
     *                                   "maxWeight": "UP TO 50 POUNDS/23 KILOG RAMS",
     *                                   "maxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
     *                                   "firstPiece": 1,
     *                                   "lastPiece": 1
     *                               }
     *                           }
     *                       }
     *                   },
     *                   {
     *                       "segmentId": 3,
     *                       "departureTime": "2019-12-07 13:40",
     *                       "arrivalTime": "2019-12-07 19:05",
     *                       "stop": 0,
     *                       "stops": null,
     *                       "flightNumber": "6692",
     *                       "bookingClass": "T",
     *                       "duration": 265,
     *                       "departureAirportCode": "CDG",
     *                       "departureAirportTerminal": "",
     *                       "arrivalAirportCode": "IST",
     *                       "arrivalAirportTerminal": "",
     *                       "operatingAirline": "AC",
     *                       "airEquipType": null,
     *                       "marketingAirline": "AC",
     *                       "cabin": "Y",
     *                       "ticket_id": 2,
     *                       "baggage": {
     *                           "ADT": {
     *                               "allowPieces": 2,
     *                               "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
     *                               "allowMaxWeight": "UP TO 50 POUNDS/23 KILOGRAMS",
     *                               "charge": {
     *                                   "price": 100,
     *                                   "maxWeight": "UP TO 50 POUNDS/23 KILOG RAMS",
     *                                   "maxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS",
     *                                   "firstPiece": 1,
     *                                   "lastPiece": 1
     *                               }
     *                           }
     *                       }
     *                   }
     *               ],
     *               "duration": 1185
     *           },
     *           {
     *               "tripId": 2,
     *               "segments": [
     *                   {
     *                       "segmentId": 1,
     *                       "departureTime": "2019-12-25 09:15",
     *                       "arrivalTime": "2019-12-25 10:35",
     *                       "stop": 0,
     *                       "stops": null,
     *                       "flightNumber": "6681",
     *                       "bookingClass": "T",
     *                       "duration": 140,
     *                       "departureAirportCode": "IST",
     *                       "departureAirportTerminal": "",
     *                       "arrivalAirportCode": "GVA",
     *                       "arrivalAirportTerminal": "",
     *                       "operatingAirline": "AC",
     *                       "airEquipType": null,
     *                       "marketingAirline": "AC",
     *                       "cabin": "Y",
     *                       "ticket_id": 1,
     *                       "baggage": {
     *                           "ADT": {
     *                               "allowPieces": 1,
     *                               "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS ‡",
     *                               "allowMaxWeight": "UP TO 50 POUNDS/23 ‡ MD« KILOGRAMS",
     *                               "charge": {
     *                                   "price": 100,
     *                                   "maxWeight": "UP TO 50 POUNDS/23 KILOG RAMS",
     *                                   "maxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS CARRY ON ALLOWANCE INDYYZ YYZCDG GVAYYZ YYZIND",
     *                                   "firstPiece": 1,
     *                                   "lastPiece": 1
     *                               }
     *                           }
     *                       }
     *                   },
     *                   {
     *                       "segmentId": 2,
     *                       "departureTime": "2019-12-25 12:00",
     *                       "arrivalTime": "2019-12-25 17:34",
     *                       "stop": 0,
     *                       "stops": null,
     *                       "flightNumber": "835",
     *                       "bookingClass": "T",
     *                       "duration": 694,
     *                       "departureAirportCode": "GVA",
     *                       "departureAirportTerminal": "",
     *                       "arrivalAirportCode": "YYZ",
     *                       "arrivalAirportTerminal": "",
     *                       "operatingAirline": "AC",
     *                       "airEquipType": null,
     *                       "marketingAirline": "AC",
     *                       "cabin": "Y",
     *                       "ticket_id": 2,
     *                       "baggage": {
     *                           "ADT": {
     *                               "allowPieces": 1,
     *                               "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS ‡",
     *                               "allowMaxWeight": "UP TO 50 POUNDS/23 ‡ MD« KILOGRAMS",
     *                               "charge": {
     *                                   "price": 100,
     *                                   "maxWeight": "UP TO 50 POUNDS/23 KILOG RAMS",
     *                                   "maxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS CARRY ON ALLOWANCE INDYYZ YYZCDG GVAYYZ YYZIND",
     *                                   "firstPiece": 1,
     *                                   "lastPiece": 1
     *                               }
     *                           }
     *                       }
     *                   },
     *                   {
     *                       "segmentId": 3,
     *                       "departureTime": "2019-12-25 20:55",
     *                       "arrivalTime": "2019-12-25 22:37",
     *                       "stop": 0,
     *                       "stops": null,
     *                       "flightNumber": "7313",
     *                       "bookingClass": "T",
     *                       "duration": 102,
     *                       "departureAirportCode": "YYZ",
     *                       "departureAirportTerminal": "",
     *                       "arrivalAirportCode": "IND",
     *                       "arrivalAirportTerminal": "",
     *                       "operatingAirline": "AC",
     *                       "airEquipType": null,
     *                       "marketingAirline": "AC",
     *                       "cabin": "Y",
     *                       "ticket_id": 2,
     *                       "baggage": {
     *                           "ADT": {
     *                               "allowPieces": 1,
     *                               "allowMaxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS ‡",
     *                               "allowMaxWeight": "UP TO 50 POUNDS/23 ‡ MD« KILOGRAMS",
     *                               "charge": {
     *                                   "price": 100,
     *                                   "maxWeight": "UP TO 50 POUNDS/23 KILOG RAMS",
     *                                   "maxSize": "UP TO 62 LINEAR INCHES/158 LINEAR CENTIMETERS CARRY ON ALLOWANCE INDYYZ YYZCDG GVAYYZ YYZIND",
     *                                   "firstPiece": 1,
     *                                   "lastPiece": 1
     *                               }
     *                           }
     *                       }
     *                   }
     *               ],
     *               "duration": 1222
     *           }
     *       ],
     *       "validatingCarrier": "AC",
     *       "fareType": "PUB",
     *       "tripType": "RT",
     *       "currency": "USD",
     *       "currencyRate": 1
     *   },
     *   "errors": [],
     *   "uid": "5cb97d1c78486",
     *   "lead_id": 92322,
     *   "lead_uid": "5cb8735a502f5",
     *   "lead_expiration_dt": "2021-02-23 20:12:12",
     *   "lead_delayed_charge": 0,
     *   "lead_status": null,
     *   "lead_type": 2,
     *   "booked_quote_uid": null,
     *   "source_code": "38T556",
     *   "agentName": "admin",
     *   "agentEmail": "admin@wowfare.com",
     *   "agentDirectLine": "",
     *   "generalEmail": "info@wowfare.com",
     *   "generalDirectLine": "+37379731662",
     *   "typeId": 2,
     *   "typeName": "Alternative",
     *   "client": {
     *       "uuid": "35009a79-1a05-49d7-b876-2b884d0f825b"
     *       "client_id": 331968,
     *       "first_name": "Johann",
     *       "middle_name": "Sebastian",
     *       "last_name": "Bach",
     *       "phones": [
     *           "+13152572166"
     *       ],
     *       "emails": [
     *           "example@test.com",
     *           "bah@gmail.com"
     *       ]
     *   },
     *   "quote": {
     *       "id": 382366,
     *       "uid": "5d43e1ec36372",
     *       "lead_id": 178363,
     *       "employee_id": 167,
     *       "record_locator": "",
     *       "pcc": "DFWG32100",
     *       "cabin": "E",
     *       "gds": "A",
     *       "trip_type": "OW",
     *       "main_airline_code": "SU",
     *       "reservation_dump": "1  SU1845T  22AUG  KIVSVO    255A    555A  TH",
     *       "status": 5,
     *       "check_payment": 1,
     *       "fare_type": "PUB",
     *       "created": "2019-08-02 07:10:36",
     *       "updated": "2019-08-05 08:58:18",
     *       "created_by_seller": 1,
     *       "employee_name": "alex.connor2",
     *       "last_ticket_date": "2019-08-09 00:00:00",
     *       "service_fee_percent": null,
     *       "pricing_info": null,
     *       "alternative": 1,
     *       "tickets": "[{\"key\":\"02_QVdBUlFBKlkxMDAwL05ZQ01BRDIwMTktMDgtMjYvTUFETllDMjAxOS0wOS0wNipVQX4jVUE1MSNVQTUw\",\"routingId\":0,\"prices\":{\"lastTicketDate\":\"2019-08-11\",\"totalPrice\":392.73,\"totalTax\":272.73,\"markup\":50,\"markupId\":0,\"isCk\":false,\"oMarkup\":{\"amount\":50,\"currency\":\"USD\"}},\"passengers\":{\"ADT\":{\"codeAs\":\"JWZ\",\"cnt\":1,\"price\":392.73,\"tax\":272.73,\"baseFare\":120,\"pubBaseFare\":120,\"baseTax\":222.73,\"markup\":50,\"refundPenalty\":\"\",\"changePenalty\":\"Percentage: 100.00%\",\"endorsementPenalty\":\"\",\"publishFare\":false,\"fareDescription\":\"\",\"oBaseFare\":{\"amount\":120,\"currency\":\"USD\"},\"oBaseTax\":{\"amount\":222.73,\"currency\":\"USD\"},\"oMarkup\":{\"amount\":50,\"currency\":\"USD\"}}},\"maxSeats\":0,\"validatingCarrier\":\"UA\",\"gds\":\"T\",\"pcc\":\"E9V\",\"fareType\":\"SR\",\"tripType\":\"RT\",\"cabin\":\"Y\",\"currency\":\"USD\",\"trips\":[{\"tripId\":1,\"segmentIds\":[1]},{\"tripId\":2,\"segmentIds\":[3]}]},{\"key\":\"02_QVdBUlFBKlkxMDAwL01BRFZJRTIwMTktMDgtMjcvVklFTUFEMjAxOS0wOS0wNSpMWH4jTFgyMDI3I0xYMzU2OCNMWDM1NjMjTFgyMDQ4\",\"routingId\":0,\"prices\":{\"lastTicketDate\":\"2019-08-09\",\"totalPrice\":305.3,\"totalTax\":184.3,\"markup\":50,\"markupId\":0,\"isCk\":false,\"oMarkup\":{\"amount\":50,\"currency\":\"USD\"}},\"passengers\":{\"ADT\":{\"codeAs\":\"ADT\",\"cnt\":1,\"price\":305.3,\"tax\":184.3,\"baseFare\":121,\"pubBaseFare\":121,\"baseTax\":134.3,\"markup\":50,\"refundPenalty\":\"Percentage: 100.00%\",\"changePenalty\":\"Percentage: 100.00%\",\"endorsementPenalty\":\"\",\"publishFare\":false,\"fareDescription\":\"\",\"oBaseFare\":{\"amount\":121,\"currency\":\"USD\"},\"oBaseTax\":{\"amount\":134.3,\"currency\":\"USD\"},\"oMarkup\":{\"amount\":50,\"currency\":\"USD\"}}},\"maxSeats\":0,\"validatingCarrier\":\"LX\",\"gds\":\"T\",\"pcc\":\"E9V\",\"fareType\":\"PUB\",\"tripType\":\"RT\",\"cabin\":\"Y\",\"currency\":\"USD\",\"trips\":[{\"tripId\":1,\"segmentIds\":[2,3]},{\"tripId\":2,\"segmentIds\":[1,2]}]}]",
     *       "origin_search_data": "{\"key\":\"01_U0FMMTAxKlkxMDAwL0pGS0ZSQTIwMTktMTEtMjEqUk9+I1FSNzA0I1FSMjI3I1JPMjk4I1JPMzAxOjNkMjBiYzI5LWIzMmItNGJhOC05OTljLTQ4ZTFlYWI1NGU1Ng==\",\"routingId\":306,\"gdsOfferId\":\"3d20bc29-b32b-4ba8-999c-48e1eab54e56\",\"prices\":{\"lastTicketDate\":\"2019-11-23\",\"totalPrice\":670.35,\"totalTax\":367.35,\"markup\":100,\"markupId\":0,\"isCk\":false,\"oMarkup\":{\"amount\":100,\"currency\":\"USD\"}},\"passengers\":{\"ADT\":{\"codeAs\":\"JWZ\",\"cnt\":1,\"price\":670.35,\"tax\":367.35,\"baseFare\":303,\"pubBaseFare\":303,\"baseTax\":267.35,\"markup\":100,\"refundPenalty\":\"Amount: USD375.00 Percentage: 100.00%\",\"changePenalty\":\"Amount: USD260.00 Percentage: 100.00%\",\"endorsementPenalty\":\" \",\"publishFare\":false,\"fareDescription\":\"\",\"oBaseFare\":{\"amount\":303,\"currency\":\"USD\"},\"oPubBaseFare\":{\"amount\":303,\"currency\":\"USD\"},\"oBaseTax\":{\"amount\":267.35,\"currency\":\"USD\"},\"oMarkup\":{\"amount\":100,\"currency\":\"USD\"}}},\"trips\":[{\"tripId\":1,\"segments\":[{\"segmentId\":1,\"departureTime\":\"2019-11-21 09:45\",\"arrivalTime\":\"2019-11-22 06:00\",\"stop\":0,\"stops\":[],\"flightNumber\":\"704\",\"bookingClass\":\"N\",\"duration\":735,\"departureAirportCode\":\"JFK\",\"departureAirportTerminal\":\"7\",\"arrivalAirportCode\":\"DOH\",\"arrivalAirportTerminal\":\"\",\"operatingAirline\":\"QR\",\"airEquipType\":\"351\",\"marketingAirline\":\"QR\",\"marriageGroup\":\"I\",\"mileage\":6689,\"cabin\":\"Y\",\"meal\":\"\",\"fareCode\":\"NLUSN1RO\",\"baggage\":{\"ADT\":{\"allowPieces\":2}},\"recheckBaggage\":false},{\"segmentId\":2,\"departureTime\":\"2019-11-22 07:10\",\"arrivalTime\":\"2019-11-22 11:25\",\"stop\":0,\"stops\":[],\"flightNumber\":\"227\",\"bookingClass\":\"N\",\"duration\":315,\"departureAirportCode\":\"DOH\",\"departureAirportTerminal\":\"\",\"arrivalAirportCode\":\"SOF\",\"arrivalAirportTerminal\":\"2\",\"operatingAirline\":\"QR\",\"airEquipType\":\"320\",\"marketingAirline\":\"QR\",\"marriageGroup\":\"O\",\"mileage\":1999,\"cabin\":\"Y\",\"meal\":\"\",\"fareCode\":\"NLUSN1RO\",\"baggage\":{\"ADT\":{\"allowPieces\":2}},\"recheckBaggage\":false},{\"segmentId\":3,\"departureTime\":\"2019-11-22 19:45\",\"arrivalTime\":\"2019-11-22 20:50\",\"stop\":0,\"stops\":[],\"flightNumber\":\"298\",\"bookingClass\":\"T\",\"duration\":65,\"departureAirportCode\":\"SOF\",\"departureAirportTerminal\":\"2\",\"arrivalAirportCode\":\"OTP\",\"arrivalAirportTerminal\":\"\",\"operatingAirline\":\"RO\",\"airEquipType\":\"AT7\",\"marketingAirline\":\"RO\",\"marriageGroup\":\"I\",\"mileage\":185,\"cabin\":\"Y\",\"meal\":\"\",\"fareCode\":\"TOWSVR\",\"baggage\":{\"ADT\":{\"allowPieces\":1}},\"recheckBaggage\":true},{\"segmentId\":4,\"departureTime\":\"2019-11-23 08:35\",\"arrivalTime\":\"2019-11-23 10:15\",\"stop\":0,\"stops\":[],\"flightNumber\":\"301\",\"bookingClass\":\"T\",\"duration\":160,\"departureAirportCode\":\"OTP\",\"departureAirportTerminal\":\"\",\"arrivalAirportCode\":\"FRA\",\"arrivalAirportTerminal\":\"2\",\"operatingAirline\":\"RO\",\"airEquipType\":\"73W\",\"marketingAirline\":\"RO\",\"marriageGroup\":\"O\",\"mileage\":903,\"cabin\":\"Y\",\"meal\":\"\",\"fareCode\":\"TOWSVR\",\"baggage\":{\"ADT\":{\"allowPieces\":1}},\"recheckBaggage\":false}],\"duration\":2550}],\"maxSeats\":0,\"validatingCarrier\":\"RO\",\"gds\":\"G\",\"pcc\":\"NA\",\"cons\":\"GIS\",\"fareType\":\"NA\",\"tripType\":\"OW\",\"cabin\":\"Y\",\"currency\":\"USD\",\"currencies\":[\"USD\"],\"currencyRates\":{\"USDUSD\":{\"from\":\"USD\",\"to\":\"USD\",\"rate\":1}},\"tickets\":[{\"key\":\"02_QVdBUlFBKlkxMDAwL0pGS1NPRjIwMTktMTEtMjEqUVJ+I1FSNzA0I1FSMjI3\",\"routingId\":0,\"prices\":{\"lastTicketDate\":\"2019-11-21\",\"totalPrice\":388.8,\"totalTax\":267.8,\"markup\":50,\"markupId\":0,\"isCk\":false,\"oMarkup\":{\"amount\":50,\"currency\":\"USD\"}},\"passengers\":{\"ADT\":{\"codeAs\":\"JWZ\",\"cnt\":1,\"price\":388.8,\"tax\":267.8,\"baseFare\":121,\"pubBaseFare\":121,\"baseTax\":217.8,\"markup\":50,\"refundPenalty\":\"Amount: USD375.00 \",\"changePenalty\":\"Amount: USD260.00\",\"endorsementPenalty\":\"\",\"publishFare\":false,\"fareDescription\":\"\",\"oBaseFare\":{\"amount\":121,\"currency\":\"USD\"},\"oBaseTax\":{\"amount\":217.8,\"currency\":\"USD\"},\"oMarkup\":{\"amount\":50,\"currency\":\"USD\"}}},\"maxSeats\":0,\"validatingCarrier\":\"QR\",\"gds\":\"T\",\"pcc\":\"E9V\",\"fareType\":\"SR\",\"tripType\":\"OW\",\"cabin\":\"Y\",\"currency\":\"USD\",\"trips\":[{\"tripId\":1,\"segmentIds\":[1,2]}]},{\"key\":\"01_QVdBUlFBKlkxMDAwL1NPRkZSQTIwMTktMTEtMjIqUk9+I1JPMjk4I1JPMzAx\",\"routingId\":0,\"prices\":{\"lastTicketDate\":\"2019-10-19\",\"totalPrice\":265.6,\"totalTax\":83.6,\"markup\":50,\"markupId\":0,\"isCk\":false,\"oMarkup\":{\"amount\":50,\"currency\":\"USD\"}},\"passengers\":{\"ADT\":{\"codeAs\":\"ADT\",\"cnt\":1,\"price\":265.6,\"tax\":83.6,\"baseFare\":182,\"pubBaseFare\":182,\"baseTax\":33.6,\"markup\":50,\"refundPenalty\":\"Percentage: 100.00%\",\"changePenalty\":\"Percentage: 100.00%\",\"endorsementPenalty\":\"\",\"publishFare\":false,\"fareDescription\":\"\",\"oBaseFare\":{\"amount\":182,\"currency\":\"USD\"},\"oBaseTax\":{\"amount\":33.6,\"currency\":\"USD\"},\"oMarkup\":{\"amount\":50,\"currency\":\"USD\"}}},\"maxSeats\":0,\"validatingCarrier\":\"RO\",\"gds\":\"T\",\"pcc\":\"E9V\",\"fareType\":\"PUB\",\"tripType\":\"OW\",\"cabin\":\"Y\",\"currency\":\"USD\",\"trips\":[{\"tripId\":1,\"segmentIds\":[3,4]}]}]}",
     *       "typeId": 2,
     *       "typeName": "Alternative",
     *       "q_client_currency": "USD",
     *       "q_client_currency_rate": "1"
     *   },
     *   "itineraryOrigin": {
     *      "uid": "5f207ec202212",
     *      "typeId": 1,
     *      "typeName": "Original"
     *   },
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
     *   "lead": {
     *       "additionalInformation": [
     *           {
     *              "pnr": "example_pnr",
     *               "bo_sale_id": "example_sale_id",
     *              "vtf_processed": null,
     *              "tkt_processed": null,
     *              "exp_processed": null,
     *              "passengers": [],
     *              "paxInfo": []
     *          }
     *      ],
     *      "lead_data": [
     *          {
     *              "ld_field_key": "example_key",
     *              "ld_field_value": "example_value"
     *          },
     *          {
     *              "ld_field_key": "example_key",
     *              "ld_field_value": "example_value"
     *          }
     *      ],
     *      "experiments": [
     *          {
     *              "cross_ex_code": "wpl5.0",
     *          },
     *          {
     *              "cross_ex_code": "wpl6.2",
     *          }
     *      ],
     *      "department_key": "chat",
     *      "type_create_id": 8,
     *      "type_create_name": "Client Chat"
     *   },
     *   "action": "v2/quote/get-info",
     *   "response_id": 298939,
     *   "request_dt": "2019-04-25 13:12:44",
     *   "response_dt": "2019-04-25 13:12:44"
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
     * @throws InvalidConfigException
     */
    public function actionGetInfo(): array
    {
        $this->checkPost();
        $apiLog = $this->startApiLog($this->action->uniqueId);

        $uid = Yii::$app->request->post('uid');
        $checkout_id = Yii::$app->request->post('checkout_id');
        $clientIP = Yii::$app->request->post('clientIP');

        if (!$uid) {
            throw new BadRequestHttpException('Not found UID on POST request', 1);
        }

        QuoteCommunicationOpenLog::createByRequestData(\Yii::$app->getRequest()->getBodyParams());

        if ($this->apiProject) {
            $projectIds = [$this->apiProject->id];
            if ($this->apiProject->projectMainRelation) {
                $projectIds = ArrayHelper::merge($projectIds, [$this->apiProject->projectMainRelation->prl_project_id]);
            }

            $model = Quote::getQuoteByUidAndProjects($uid, $projectIds);
        } else {
            $model = Quote::find()->where(['uid' => $uid])->one();
        }

        if (!$model) {
            throw new NotFoundHttpException('Not found Quote UID: ' . $uid, 2);
        }

        $response = [
            'status' => 'Failed',
            'result' => [],
            'errors' => []
        ];

        try {
            if ($checkout_id && !$model->lead->hybrid_uid) {
                $model->lead->hybrid_uid = $checkout_id;
                $model->lead->save();
            }

            $response['status'] = $model->status != $model::STATUS_DECLINED ? 'Success' : 'Failed';

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
            $response['lead_expiration_dt'] = $model->lead->l_expiration_dt;
            $response['lead_delayed_charge'] = $model->lead->l_delayed_charge;
            $response['lead_status'] = null;
            $response['lead_type'] = $model->lead->l_type;
            $response['booked_quote_uid'] = null;
            $response['source_code'] = ($model->lead && isset($model->lead->source)) ? $model->lead->source->cid : null;

            if (in_array($model->lead->status, [Lead::STATUS_SOLD, Lead::STATUS_BOOKED])) {
                $response['lead_status'] = $model->lead->status == Lead::STATUS_SOLD ? 'sold' : 'booked';
                $response['booked_quote_uid'] = $model->lead->getBookedQuoteUid();
            }

            $response['agentName'] = ($model->lead && $model->lead->employee) ? $model->lead->employee->username : null;
//            $response['agentEmail'] = $userProjectParams ? $userProjectParams->upp_email : $model->lead->project->contactInfo->email;
            $response['agentEmail'] = ($userProjectParams && $userProjectParams->getEmail()) ? $userProjectParams->getEmail() : $model->lead->project->contactInfo->email;
//            $response['agentDirectLine'] = $userProjectParams ? $userProjectParams->upp_tw_phone_number : sprintf('%s', $model->lead->project->contactInfo->phone);
            $response['agentDirectLine'] = ($userProjectParams && $userProjectParams->getPhone()) ? $userProjectParams->getPhone() : sprintf('%s', $model->lead->project->contactInfo->phone);
            $response['generalEmail'] = $model->lead->project->contactInfo->email;
            $response['generalDirectLine'] = sprintf('%s', $model->lead->project->contactInfo->phone);

            /** @var Lead $lead */
            $lead = $model->lead;
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

            $response['quote'] = $model->attributes;
            $response['quote']['typeId'] = $model->type_id;
            $response['quote']['typeName'] = Quote::getTypeName($model->type_id);

            if ($model->isAlternative() && $originalQuote = Quote::getOriginalQuoteByLeadId($model->lead_id)) {
                $response['itineraryOrigin']['uid'] = $originalQuote->uid;
                $response['itineraryOrigin']['typeId'] = $originalQuote->type_id;
                $response['itineraryOrigin']['typeName'] = Quote::getTypeName($originalQuote->type_id);
            }

            if ($lead) {
                $response['lead']['department_key'] = $lead->lDep->dep_key ?? null;
                $response['lead']['type_create_id'] = $lead->l_type_create ?? null;
                $response['lead']['type_create_name'] = $lead->getTypeCreateName();

                ArrayHelper::setValue(
                    $response,
                    'lead.additionalInformation',
                    $lead->additional_information ? JsonHelper::decode($lead->additional_information) : ''
                );
                ArrayHelper::setValue(
                    $response,
                    'lead.lead_data',
                    LeadDataService::getByLeadForApi($lead)
                );
            }

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

            try {
                if (!$model->isClientCurrencyDefault()) {
                    $clientPriceData = (new ClientQuotePriceService($model))->getClientQuotePricePassengersData();
                    $response['result'] = array_merge($response['result'], $clientPriceData);
                }
            } catch (\Throwable $throwable) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['quoteId' => $model->id]);
                \Yii::error($message, 'QuoteController:actionGetInfo:Throwable');
            }

            $response['result']['trips'] = $model->getQuoteTripsData();

            if ((int)$model->status === Quote::STATUS_SENT) {
                $excludeIP = Quote::isExcludedIP($clientIP);
                if (!$excludeIP) {
                    $model->status = Quote::STATUS_OPENED;
                    if ($model->save()) {
                        $lead = $model->lead;
                        if ($lead) {
                            $project_name = $lead->project ? $lead->project->name : '';
                            $subject = 'Quote- ' . $model->uid . ' OPENED';
                            $message = 'Your Quote (UID: ' . $model->uid . ") has been OPENED by client! \r\nProject: " . Html::encode($project_name) . "! \r\n Lead (Id: " . Purifier::createLeadShortLink($lead) . ")";

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
        $responseData = $apiLog->endApiLog($responseData);

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

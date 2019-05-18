<?php

namespace webapi\modules\v2\controllers;

use common\models\Quote;
use common\models\UserProjectParams;
use Yii;
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
    "lead_status": null,
    "booked_quote_uid": null,
    "agentName": "admin",
    "agentEmail": "admin@wowfare.com",
    "agentDirectLine": "",
    "generalEmail": "info@wowfare.com",
    "generalDirectLine": "+37379731662",
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

            $response['status'] = ($model->status != $model::STATUS_DECLINED) ? 'Success' : 'Failed';

            $userProjectParams = UserProjectParams::findOne([
                'upp_user_id' => $model->lead->employee_id,
                'upp_project_id' => $model->lead->project_id
            ]);

            $response['uid'] = $uid;
            $response['lead_id'] = $model->lead->id;
            $response['lead_uid'] = $model->lead->uid;
            $response['lead_status'] = null;
            $response['booked_quote_uid'] = null;

            if(in_array($model->lead->status,[10,12])){
                $response['lead_status'] = ($model->lead->status == 10)?'sold':'booked';
                $response['booked_quote_uid'] = $model->lead->getBookedQuoteUid();
            }

            $response['agentName'] = ($model->lead & $model->lead->employee) ? $model->lead->employee->username : null;
            $response['agentEmail'] = $userProjectParams ? $userProjectParams->upp_email : $model->lead->project->contactInfo->email;
            $response['agentDirectLine'] = $userProjectParams ? $userProjectParams->upp_phone_number : sprintf('%s', $model->lead->project->contactInfo->phone);
            $response['generalEmail'] = $model->lead->project->contactInfo->email;
            $response['generalDirectLine'] = sprintf('%s', $model->lead->project->contactInfo->phone);

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

            if ($model->status == Quote::STATUS_SEND) {
                $excludeIP = Quote::isExcludedIP($clientIP);
                if(!$excludeIP){
                    $model->status = Quote::STATUS_OPENED;
                    $model->save();
                    exec(dirname(Yii::getAlias('@app')) . '/yii quote/send-opened-notification '.$uid.'  > /dev/null 2>&1 &');
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

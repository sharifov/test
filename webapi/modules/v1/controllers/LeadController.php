<?php
namespace webapi\modules\v1\controllers;

use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Lead;
use common\models\LeadFlightSegment;
use webapi\models\ApiLead;
use Yii;
use yii\web\BadRequestHttpException;
use yii\web\UnprocessableEntityHttpException;


class LeadController extends ApiBaseController
{


    /**
     *
     * @api {post} /v1/lead/create Create Lead
     * @apiVersion 0.1.0
     * @apiName CreateLead
     * @apiGroup Leads
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization    Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {object}           lead                                               Lead data array
     * @apiParam {int}                  lead.source_id                                     Source ID
     * @apiParam {int{1..9}}            lead.adults                                        Adult count
     * @apiParam {string{1}=E-ECONOMY,B-BUSINESS,F-FIRST,P-PREMIUM}        lead.cabin                                         Cabin
     * @apiParam {array[]}              lead.emails                                         Array of Emails (string)
     * @apiParam {array[]}              lead.phones                                         Array of Phones (string)
     * @apiParam {object[]}             lead.flights                                        Array of Flights
     * @apiParam {string{3}}                                lead.flights.origin                 Flight Origin location Airport IATA-code
     * @apiParam {string{3}}                                lead.flights.destination            Flight Destination location Airport IATA-code
     * @apiParam {datetime{YYYY-MM-DD HH:II:SS}}            lead.flights.departure              Flight Departure DateTime (format YYYY-MM-DD HH:ii:ss)
     * @apiParam {string{2}=OW-ONE_WAY,RT-ROUND_TRIP,MC-MULTI_DESTINATION}        [lead.trip_type]                                         Trip type (if empty - autocomplete)
     * @apiParam {int=1-PENDING,2-PROCESSING,4-REJECT,5-FOLLOW_UP,8-ON_HOLD,10-SOLD,11-TRASH,12-BOOKED,13-SNOOZE}        [lead.status]                                       Status
     *
     * @apiParam {int{0..9}}            [lead.children]                                      Children count
     * @apiParam {int{0..9}}            [lead.infants]                                       Infant count
     * @apiParam {string{40}}           [lead.uid]                                           UID value
     * @apiParam {text}                 [lead.notes_for_experts]                             Notes for expert
     * @apiParam {text}                 [lead.request_ip_detail]                             Request IP detail (autocomplete)
     * @apiParam {string{50}}           [lead.request_ip]                                    Request IP
     * @apiParam {int}                  [lead.snooze_for]                                    Snooze for
     * @apiParam {int}                  [lead.rating]                                        Rating
     *
     * @apiParam {string{3..100}}       [lead.client_first_name]                            Client first name
     * @apiParam {string{3..100}}       [lead.client_last_name]                             Client last name
     *
     *
     *
     * @apiParamExample {json} Request-Example:
     * {
     *    "lead": {
     *        "flights": [
     *            {
     *                "origin": "KIV",
     *                "destination": "DME",
     *                "departure": "2018-10-13 13:50:00",
     *            },
     *            {
     *                "origin": "DME",
     *                "destination": "KIV",
     *                "departure": "2018-10-18 10:54:00",
     *            }
     *        ],
     *        "emails": [
     *          "email1@gmail.com",
     *          "email2@gmail.com",
     *        ],
     *        "phones": [
     *          "+373-69-487523",
     *          "022-45-7895-89",
     *        ],
     *        "source_id": 38,
     *        "adults": 1,
     *        "client_first_name": "Alexandr",
     *        "client_last_name": "Freeman"
     *    }
     * }
     *
     * @apiSuccess {Integer} response_id    Response Id
     * @apiSuccess {DateTime} request_dt    Request Date & Time
     * @apiSuccess {DateTime} response_dt   Response Date & Time
     * @apiSuccess {Array} data Data Array
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
    {
        "status": 200,
        "name": "Success",
        "code": 0,
        "message": "",
        "data": {
            "response": {
                "lead": {
                    "client_id": 11,
                    "employee_id": null,
                    "status": 1,
                    "uid": "5b73b80eaf69b",
                    "project_id": 6,
                    "source_id": "38",
                    "trip_type": "RT",
                    "cabin": "E",
                    "adults": "1",
                    "children": 0,
                    "infants": 0,
                    "notes_for_experts": null,
                    "created": "2018-08-15 05:20:14",
                    "updated": "2018-08-15 05:20:14",
                    "request_ip": "127.0.0.1",
                    "request_ip_detail": "{\"ip\":\"127.0.0.1\",\"city\":\"North Pole\",\"postal\":\"99705\",\"state\":\"Alaska\",\"state_code\":\"AK\",\"country\":\"United States\",\"country_code\":\"US\",\"location\":\"64.7548317,-147.3431046\",\"timezone\":{\"id\":\"America\\/Anchorage\",\"location\":\"61.21805,-149.90028\",\"country_code\":\"US\",\"country_name\":\"United States of America\",\"iso3166_1_alpha_2\":\"US\",\"iso3166_1_alpha_3\":\"USA\",\"un_m49_code\":\"840\",\"itu\":\"USA\",\"marc\":\"xxu\",\"wmo\":\"US\",\"ds\":\"USA\",\"phone_prefix\":\"1\",\"fifa\":\"USA\",\"fips\":\"US\",\"gual\":\"259\",\"ioc\":\"USA\",\"currency_alpha_code\":\"USD\",\"currency_country_name\":\"UNITED STATES\",\"currency_minor_unit\":\"2\",\"currency_name\":\"US Dollar\",\"currency_code\":\"840\",\"independent\":\"Yes\",\"capital\":\"Washington\",\"continent\":\"NA\",\"tld\":\".us\",\"languages\":\"en-US,es-US,haw,fr\",\"geoname_id\":\"6252001\",\"edgar\":\"\"},\"datetime\":{\"date\":\"08\\/14\\/2018\",\"date_time\":\"08\\/14\\/2018 21:20:15\",\"date_time_txt\":\"Tuesday, August 14, 2018 21:20:15\",\"date_time_wti\":\"Tue, 14 Aug 2018 21:20:15 -0800\",\"date_time_ymd\":\"2018-08-14T21:20:15-08:00\",\"time\":\"21:20:15\",\"month\":\"8\",\"month_wilz\":\"08\",\"month_abbr\":\"Aug\",\"month_full\":\"August\",\"month_days\":\"31\",\"day\":\"14\",\"day_wilz\":\"14\",\"day_abbr\":\"Tue\",\"day_full\":\"Tuesday\",\"year\":\"2018\",\"year_abbr\":\"18\",\"hour_12_wolz\":\"9\",\"hour_12_wilz\":\"09\",\"hour_24_wolz\":\"21\",\"hour_24_wilz\":\"21\",\"hour_am_pm\":\"pm\",\"minutes\":\"20\",\"seconds\":\"15\",\"week\":\"33\",\"offset_seconds\":\"-28800\",\"offset_minutes\":\"-480\",\"offset_hours\":\"-8\",\"offset_gmt\":\"-08:00\",\"offset_tzid\":\"America\\/Anchorage\",\"offset_tzab\":\"AKDT\",\"offset_tzfull\":\"Alaska Daylight Time\",\"tz_string\":\"AKST+9AKDT,M3.2.0\\/2,M11.1.0\\/2\",\"dst\":\"true\",\"dst_observes\":\"true\",\"timeday_spe\":\"evening\",\"timeday_gen\":\"evening\"}}",
                    "offset_gmt": "-08.00",
                    "snooze_for": null,
                    "rating": null,
                    "id": 7
                },
                "flights": [
                    {
                        "origin": "BOS",
                        "destination": "LGW",
                        "departure": "2018-09-19"
                    },
                    {
                        "origin": "LGW",
                        "destination": "BOS",
                        "departure": "2018-09-22"
                    }
                ],
                "emails": [
                    "chalpet@gmail.com",
                    "chalpet2@gmail.com"
                ],
                "phones": [
                    "+373-69-98-698",
                    "+373-69-98-698"
                ]
            },
            "request": {
                "client_id": null,
                "employee_id": null,
                "status": null,
                "uid": null,
                "project_id": 6,
                "source_id": "38",
                "trip_type": null,
                "cabin": null,
                "adults": "1",
                "children": null,
                "infants": null,
                "notes_for_experts": null,
                "created": null,
                "updated": null,
                "request_ip": null,
                "request_ip_detail": null,
                "offset_gmt": null,
                "snooze_for": null,
                "rating": null,
                "flights": [
                    {
                        "origin": "BOS",
                        "destination": "LGW",
                        "departure": "2018-09-19"
                    },
                    {
                        "origin": "LGW",
                        "destination": "BOS",
                        "departure": "2018-09-22"
                    }
                ],
                "emails": [
                    "chalpet@gmail.com",
                    "chalpet2@gmail.com"
                ],
                "phones": [
                    "+373-69-98-698",
                    "+373-69-98-698"
                ],
                "client_first_name": "Alexandr",
                "client_last_name": "Freeman"
            }
        },
        "action": "v1/lead/create",
        "response_id": 42,
        "request_dt": "2018-08-15 05:20:14",
        "response_dt": "2018-08-15 05:20:15"
    }
     *
     * @apiError UserNotFound The id of the User was not found.
     *
     * @apiErrorExample Error-Response:
     *      HTTP/1.1 422 Unprocessable entity
     *      {
     *          "name": "Unprocessable entity",
     *          "message": "Flight [0]: Destination should contain at most 3 characters.",
     *          "code": 5,
     *          "status": 422,
     *          "type": "yii\\web\\UnprocessableEntityHttpException"
     *      }
     *
     *
     * @return mixed
     * @throws BadRequestHttpException
     * @throws UnprocessableEntityHttpException
     * @throws \yii\db\Exception
     */

    public function actionCreate()
    {

        $this->checkPost();
        $apiLog = $this->startApiLog($this->action->uniqueId);
        $modelLead = new ApiLead();

        //print_r($this->apiProject); exit;

        if($this->apiProject) $modelLead->project_id = $this->apiProject->id;

        if($modelLead->load(Yii::$app->request->post())) {
            if (!$modelLead->validate()) {
                if ($errors = $modelLead->getErrors()) {
                    throw new UnprocessableEntityHttpException($this->errorToString($errors), 5);
                } else throw new UnprocessableEntityHttpException('Not validate Api Lead data', 5);
            }
        } else {
            throw new BadRequestHttpException('Not found Lead data on POST request', 6);
        }

        $response = [];
        $transaction = Yii::$app->db->beginTransaction();

        $client = new Client();

        if($modelLead->client_first_name) $client->first_name = $modelLead->client_first_name;
            else $client->first_name = 'ClientName';

        if($modelLead->client_last_name) $client->last_name = $modelLead->client_last_name;

        if(!$client->save()) {
            throw new UnprocessableEntityHttpException($this->errorToString($client->errors));
        }


        $lead = new Lead();
        //$lead->scenario = Lead::SCENARIO_API;
        $lead->attributes = $modelLead->attributes;

        $lead->client_id = $client->id;
        if(!$lead->status) $lead->status = Lead::STATUS_PENDING;
        if(!$lead->uid) $lead->uid = uniqid();


        if(!$lead->trip_type) $lead->trip_type = Lead::TYPE_ROUND_TRIP;

        if($modelLead->flights) {
            $flightCount = count( $modelLead->flights);

            if($flightCount == 1) $lead->trip_type = Lead::TYPE_ONE_WAY;
                elseif($flightCount == 2) $lead->trip_type = Lead::TYPE_ROUND_TRIP;
                    else $lead->trip_type = Lead::TYPE_MULTI_DESTINATION;
        }


        if(!$lead->cabin) $lead->cabin = Lead::CABIN_ECONOMY;

        if(!$lead->children) $lead->children = 0;
        if(!$lead->infants) $lead->infants = 0;
        if(!$lead->request_ip) $lead->request_ip = Yii::$app->request->remoteIP;


        if($this->apiProject) $lead->project_id = $this->apiProject->id;

        if (!$lead->validate()) {
            if ($errors = $lead->getErrors()) {
                throw new UnprocessableEntityHttpException($this->errorToString($errors), 7);
            } else throw new UnprocessableEntityHttpException('Not validate Lead data', 7);
        }

        if(!$lead->save()) {
            Yii::error(print_r($lead->errors, true), 'API:Lead:create:Lead:save');
            $transaction->rollBack();
            throw new UnprocessableEntityHttpException($this->errorToString($modelLead->errors));
        }


        if($modelLead->flights)
            foreach ($modelLead->flights as $flight) {
                $flightModel = new LeadFlightSegment();

                $flightModel->lead_id = $lead->id;
                $flightModel->origin = $flight['origin'];
                $flightModel->destination = $flight['destination'];
                $flightModel->departure = $flight['departure'];

                if(!$flightModel->save()) {
                    Yii::error(print_r($flightModel->errors, true), 'API:Lead:create:LeadFlightSegment:save');
                    $transaction->rollBack();
                } else {

                }
            }


        if($modelLead->emails)
            foreach ($modelLead->emails as $email) {
                $emailModel = new ClientEmail();

                $emailModel->client_id = $client->id;
                $emailModel->email = $email;
                $emailModel->created = date('Y-m-d H:i:s');

                if(!$emailModel->save()) {
                    Yii::error(print_r($emailModel->errors, true), 'API:Lead:create:ClientEmail:save');
                    $transaction->rollBack();
                } else {

                }
            }

        if($modelLead->phones)
            foreach ($modelLead->phones as $phone) {
                $phoneModel = new ClientPhone();

                $phoneModel->client_id = $client->id;
                $phoneModel->phone = $phone;
                $phoneModel->created = date('Y-m-d H:i:s');

                if(!$phoneModel->save()) {
                    Yii::error(print_r($phoneModel->errors, true), 'API:Lead:create:ClientPhone:save');
                    $transaction->rollBack();
                } else {


                }
            }

        $transaction->commit();

        

        try {
            $response['lead'] = $lead;
            $response['flights'] = $modelLead->flights;
            $response['emails'] = $modelLead->emails;
            $response['phones'] = $modelLead->phones;

        } catch (\Throwable $e) {

            $transaction->rollBack();
            Yii::error($e->getTraceAsString(), 'API:lead:create:try');
            if(Yii::$app->request->get('debug')) $message = ($e->getTraceAsString());
                else $message = $e->getMessage().' (code:'.$e->getCode().', line: '.$e->getLine().')';

            $response['error'] = $message;
            $response['error_code'] = 30;

        }


        if(isset($response['error']) && $response['error']) {

        } else {
            $responseData['status']     = 200;
            $responseData['name']       = 'Success';
            $responseData['code']       = 0;
            $responseData['message']    = '';
        }


        $responseData['data']['response']               = $response;
        $responseData['data']['request']                = $modelLead;



        $responseData = $apiLog->endApiLog($responseData);

        if(isset($response['error']) && $response['error']) {
            $json = @json_encode($response['error']);
            if(isset($response['error_code']) && $response['error_code']) $error_code = $response['error_code'];
            else $error_code = 0;
            throw new UnprocessableEntityHttpException($json, $error_code);
        }

        return $responseData;
    }


}

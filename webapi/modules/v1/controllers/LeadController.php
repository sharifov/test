<?php
namespace webapi\modules\v1\controllers;

use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Lead;
use common\models\LeadFlightSegment;
use common\models\Project;
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
     * @apiParam {object}           lead                                                 Trip data array
     * @apiParam {object[]}         lead.emails                                         Array of Emails
     * @apiParam {string{3}}            trip.flights.fl_origin_location_code                 Origin location Airport IATA-code
     * @apiParam {string{3}}            trip.flights.fl_destination_location_code            Destination location Airport IATA-code
     *
     * @apiParam {object[]}             trip.flights.segments                                Array of Air segments
     * @apiParam {string{3}}                trip.flights.segments.seg_departure_airport_code     Departure airport IATA-code
     * @apiParam {string{3}}                trip.flights.segments.seg_arrival_airport_code       Arrival airport IATA-code
     * @apiParam {datetime{YYYY-MM-DD HH:II:SS}}                 trip.flights.segments.seg_departure_dt       Departure DateTime (format YYYY-MM-DD HH:ii:ss)
     * @apiParam {string{2}}                trip.flights.segments.seg_airline_code       Airline code
     * @apiParam {string{3}}                trip.flights.segments.seg_flight_number       Flight number
     * @apiParam {int=1-ECONOM,2-PREMIUM_ECONOM,3-BUSSINES,4-PREMIUM_BUSINESS,5-FIRST,6-PREMIUM_FIRST}            trip.flights.segments.seg_cabin_type_id       Cabin type Id
     * @apiParam {string{1}=A,B,C,D,E,F,G,H,J,K,L,M,N,P,Q,R,S,T,U,V,W,X,Y,Z}                trip.flights.segments.seg_booking_class       Booking class
     *
     * @apiParam {object[]}         trip.passengers                                         Array of Passengers
     * @apiParam {string{3..50}}            trip.passengers.pas_first_name                   First Name
     * @apiParam {string{3..50}}            trip.passengers.pas_last_name                    Last Name
     * @apiParam {int=1-ADULT,2-CHILDREN,3-INFANT,4-INFANT_WITH_SEAT,5-SPECIFIC_CHILD}            trip.passengers.pas_type_id                      Passenger Type Id
     *
     * @apiParam {string{2}}            trip.passengers.pas_country                      Country code
     * @apiParam {string{2}}            trip.passengers.pas_nationality                  Nationality Country code
     * @apiParam {date{YYYY-MM-DD}}                 [trip.passengers.pas_dob]                        Date of birthday (format YYYY-MM-DD)
     * @apiParam {int=1-Mail,2-Femail}            trip.passengers.pas_gender_id                    Gender Type Id
     * @apiParam {string{20}}            [trip.passengers.pas_doc_number]                 Document number
     * @apiParam {date{YYYY-MM-DD}}            [trip.passengers.pas_doc_expiration_date]        Document expiration date
     *
     * @apiParam {string{50}}       trip.contact_phone                                   Contact Phone
     * @apiParam {string{160}}      [trip.contact_email]                                 Contact Email
     * @apiParam {int=1-SABRE,2-AMADEUS,3-TRAVELPORT}       [trip.gds_id]                Global Distribution System ID (Recommended GDS for Booking)
     * @apiParam {decimal}          [trip.total_price]                                   Maximal total price in USD
     *
     * @apiParamExample {json} Request-Example:
     * {
     *    "trip": {
     *        "flights": [
     *            {
     *                "fl_origin_location_code": "KIV",
     *                "fl_destination_location_code": "DME",
     *                "segments": [
     *                    {
     *                        "seg_departure_airport_code": "KIV",
     *                        "seg_arrival_airport_code": "DME",
     *                        "seg_departure_dt": "2018-10-13 13:50:00",
     *                        "seg_airline_code": "9U",
     *                        "seg_flight_number": "171",
     *                        "seg_cabin_type_id": "1",
     *                        "seg_booking_class": "A"
     *                    }
     *                ]
     *            }
     *        ],
     *        "passengers": [
     *            {
     *                "pas_first_name": "Alexandr",
     *                "pas_last_name": "Ivanov",
     *                "pas_type_id": "1",
     *                "pas_country": "MD",
     *                "pas_nationality": "MD",
     *                "pas_dob": "1983-02-03",
     *                "pas_gender_id": "1",
     *                "pas_doc_number": "1234567890",
     *                "pas_doc_expiration_date": "2020-01-01"
     *            },
     *            {
     *                "pas_first_name": "Valentina",
     *                "pas_last_name": "Petrova",
     *                "pas_type_id": "2",
     *                "pas_country": "US",
     *                "pas_nationality": "UA",
     *                "pas_dob": "1981-12-25",
     *                "pas_gender_id": "2",
     *                "pas_doc_number": "2345678901",
     *                "pas_doc_expiration_date": "2020-01-01"
     *            }
     *        ],
     *        "contact_phone": "248-111-123123",
     *        "contact_email": "test-cod2018@gmail.com",
     *        "gds_id": 1,
     *        "total_price": 500.21
     *    }
     * }
     *
     * @apiSuccess {Integer} response_id    Response Id
     * @apiSuccess {DateTime} request_dt    Request Date & Time
     * @apiSuccess {DateTime} response_dt   Response Date & Time
     * @apiSuccess {Array} process  Process Array
     * @apiSuccess {Array} data Data Array
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *      {
     *         "status": 200,
     *         "name": "Success",
     *         "code": 0,
     *         "message": "",
     *         "data": {
     *             "response": {
     *                 "error": "",
     *                 "error_code": 0,
     *                 "rule_logs": [
     *                     {
     *                     "rs_id": 6,
     *                     "rule_id": 4,
     *                     "scheduler": true,
     *                     "rule": true,
     *                     "gds": "[\"1\"]"
     *                     }
     *                 ],
     *                 "rule_id": 6,
     *                 "gds_id": 1,
     *                 "gds_pcc": "U2JF",
     *                 "pnr": "JYTNYF",
     *                 "total_price": "69.20",
     *                 "trip_id": 246
     *             },
     *             "request": {
     *                 "flights": [
     *                     {
     *                         "fl_origin_location_code": "LAX",
     *                         "fl_destination_location_code": "DFW",
     *                         "segments": [
     *                             {
     *                             "seg_departure_airport_code": "LAX",
     *                             "seg_arrival_airport_code": "DFW",
     *                             "seg_departure_dt": "2018-09-21 18:45:00",
     *                             "seg_airline_code": "UA",
     *                             "seg_flight_number": "5609",
     *                             "seg_cabin_type_id": "1",
     *                             "seg_booking_class": "N"
     *                             }
     *                         ]
     *                     }
     *                 ],
     *                 "passengers": [
     *                 {
     *                 "pas_first_name": "Alexandr",
     *                 "pas_last_name": "Ivanov",
     *                 "pas_type_id": "1",
     *                 "pas_country": "MD",
     *                 "pas_nationality": "MD",
     *                 "pas_dob": "1983-02-03",
     *                 "pas_gender_id": "1",
     *                 "pas_doc_number": "1234567890",
     *                 "pas_doc_expiration_date": "2020-01-01"
     *                 }
     *                 ],
     *                 "contact_phone": "248-111-123123",
     *                 "contact_email": "ac@zeit.style"
     *                 "gds_id": 1,
     *                 "total_price": 500.21
     *             }
     *         },
     *         "action": "v1/book/create",
     *         "response_id": 298,
     *         "request_dt": "2018-06-18 10:19:47",
     *         "response_dt": "2018-06-18 10:20:06"
     *      }
     *
     * @apiError UserNotFound The id of the User was not found.
     *
     * @apiErrorExample Error-Response:
     *      HTTP/1.1 422 Unprocessable entity
     *      {
     *          "name": "Unprocessable entity",
     *          "message": "Passenger [0]: {\"pas_type_id\":[\"Type ID must be an integer.\"]}",
     *          "code": 0,
     *          "status": 422,
     *          "type": "yii\\web\\HttpException"
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

        //print_r(123); exit;


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
        $lead->status = Lead::STATUS_PENDING;
        $lead->uid = uniqid();



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

        $lead->request_ip = Yii::$app->request->remoteIP;



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




    /**
     *
     * @api {post} /v1/book/info Get Booking info
     * @apiVersion 0.1.0
     * @apiName BookingInfo
     * @apiGroup Booking
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization    Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {object}                                   gds                 Object
     * @apiParam {string{20}}                               gds.gds_pcc         Pseudo city code / Office Id / Organization
     * @apiParam {int=1-SABRE,2-AMADEUS,3-TRAVELPORT}       gds.gds_id          Global Distribution System ID
     * @apiParam {array}                                    data                Array of data field
     * @apiParam {string{6}}                                data.pnr            PNR Code
     *
     *
     * @apiParamExample {json} Request-Example:
     *  {
     *      "gds": {
     *          "gds_pcc": "U2JF",
     *          "gds_id": 1
     *      },
     *      "data": {
     *          "pnr": "ABCDEF"
     *      }
     *  }
     *
     * @apiSuccess {Integer}    status          Status Id
     * @apiSuccess {String}     name            Name
     * @apiSuccess {Integer}    code            Code
     * @apiSuccess {String}     message         Message
     * @apiSuccess {Integer}    response_id     Response Id
     * @apiSuccess {DateTime}   request_dt      Request Date & Time
     * @apiSuccess {DateTime}   response_dt     Response Date & Time
     * @apiSuccess {Array}      data            Data Array
     *
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 OK
     *{
     *  "status": 200,
     *  "name": "Success",
     *  "code": 0,
     *  "message": "",
     *  "data": {
     *      "response": {}
     *      "request": {
     *          "data": {
     *              "pnr": "BTFARU"
     *          },
     *          "gds": {
     *              "gds_id": "1",
     *              "gds_pcc": "default"
     *          }
     *      }
     *  },
     *  "response_id": 229,
     *  "request_dt": "2018-06-14 17:49:31",
     *  "response_dt": "2018-06-14 17:49:36"
     *}
     *
     *
     * @apiError PostRequestNotFound    Not found POST request.
     * @apiError PostRequestIsEmpty     POST data request is empty.
     * @apiError TicketDataNotFound     Not found Ticket data on POST request.
     *
     * @apiErrorExample Error-Response:
     *      HTTP/1.1 422 Unprocessable entity
     *      {
     *          "name": "Unprocessable entity",
     *          "message": "\"SOAP Error AirTicketLLSRQ [ERR.SWS.HOST.ERROR_IN_RESPONSE]: Array\\n(\\n    [0] => UNABLE TO PROCESS - CORRECT\\/RETRY - 395\\n    [1] => ELECTRONIC TICKET\\/DOCUMENT EXISTS IN AIRLINE SYSTEM\\n)\\n\"",
     *          "code": 0,
     *          "status": 422,
     *          "type": "yii\\web\\UnprocessableEntityHttpException"
     *      }
     *
     *
     * @return mixed
     * @throws BadRequestHttpException
     * @throws UnprocessableEntityHttpException
     */
    public function actionInfo()
    {

        $this->checkPost();
        $apiLog = $this->startApiLog($this->action->uniqueId);

        $modelData = new ApiData();
        $modelGds = new ApiGds();

        if($modelGds->load(Yii::$app->request->post())) {
            if (!$modelGds->validate()) {
                if ($errors = $modelGds->getErrors()) {
                    throw new UnprocessableEntityHttpException($this->errorToString($errors), 3);
                } else throw new UnprocessableEntityHttpException('Not validate GDS data', 3);
            }
        } else {
            throw new BadRequestHttpException('Not found GDS data on POST request', 4);
        }


        if($modelData->load(Yii::$app->request->post())) {
            if (!$modelData->validate()) {
                if ($errors = $modelData->getErrors()) {
                    throw new UnprocessableEntityHttpException($this->errorToString($errors), 5);
                } else throw new UnprocessableEntityHttpException('Not validate GDS data', 5);
            }
        } else {
            throw new BadRequestHttpException('Not found Data on POST request', 6);
        }


        $response = [];

        try {

            if ($modelGds->gds_id == Gds::GDS_SABRE) {

                $gds = Yii::$app->sabre;
                $initGds = $gds->initPcc($modelGds->gds_pcc);

                if($initGds) {
                    $response = $gds->getPnr($modelData->pnr);
                } else {
                    $response['error'] = 'Error: Not init Sabre GDS';
                    $response['error_code'] = 37;
                }

            } else if ($modelGds->gds_id == Gds::GDS_AMADEUS) {

                $gds = Yii::$app->amadeus;
                $initGds = $gds->initPcc($modelGds->gds_pcc);
                if($initGds) {
                    $response = $gds->getPnr($modelData->pnr);
                } else {
                    $response['error'] = 'Error: Not init Amadeus GDS';
                    $response['error_code'] = 38;
                }

            } else if ($modelGds->gds_id == Gds::GDS_TRAVELPORT) {

                $gds = Yii::$app->travelport;
                $initGds = $gds->initPcc($modelGds->gds_pcc);
                if($initGds) {
                    //$response = $gds->getPnr($modelData->pnr);
                    $response = ['error' => 'travelport - Local Service is disabled'];
                } else {
                    $response['error'] = 'Error: Not init TravelPort GDS';
                    $response['error_code'] = 39;
                }

            }

        } catch (\Throwable $e) {

            Yii::error($e->getTraceAsString(), 'API:book:info:try');
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
            $responseData['message']    = 'ok';
        }


        $responseData['data']['response']               = $response;
        $responseData['data']['request']['data']        = $modelData;
        $responseData['data']['request']['gds']         = $modelGds;

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

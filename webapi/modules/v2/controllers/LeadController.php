<?php

namespace webapi\modules\v2\controllers;

use common\models\ClientEmail;
use common\models\ClientPhone;
use sales\model\lead\LeadCodeException;
use sales\model\lead\useCases\lead\api\create\LeadCreateMessage;
use sales\model\lead\useCases\lead\api\create\LeadCreateValue;
use webapi\src\Messages;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\DataMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use Yii;
use sales\model\lead\useCases\lead\api\create\LeadCreateHandler;
use sales\model\lead\useCases\lead\api\create\LeadCreateForm;
use webapi\src\logger\ApiLogger;
use webapi\src\response\ErrorResponse;
use webapi\src\response\Response;
use webapi\src\response\SuccessResponse;

/**
 * Class LeadController
 *
 * @property LeadCreateHandler $leadCreateHandler
 */
class LeadController extends BaseController
{
    private $leadCreateHandler;

    public function __construct(
        $id,
        $module,
        ApiLogger $logger,
        LeadCreateHandler $leadCreateHandler,
        $config = []
    )
    {
        parent::__construct($id, $module, $logger, $config);
        $this->leadCreateHandler = $leadCreateHandler;
    }

    /**
     * @api {post} /v2/lead/create Create Lead Alternative
     * @apiVersion 0.2.0
     * @apiName CreateLeadAlternative
     * @apiGroup Leads
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {object}               lead                                        Lead data array
     * @apiParam {string{20}}           lead.source_code                            Source Code
     * @apiParam {int{0..9}}            lead.adults                                 Adult count
     * @apiParam {int{0..9}}            lead.children                               Children count
     * @apiParam {int{0..9}}            lead.infants                                Infants count
     * @apiParam {string{50}}           [lead.request_ip]                           Request IP
     * @apiParam {string{32}}           [lead.discount_id]                          Discount ID
     * @apiParam {string{40}}           lead.uid                                    UID value
     * @apiParam {text}                 [lead.user_agent]                           User agent info
     * @apiParam {object[]}             lead.segments                               Segments
     * @apiParam {string{3}}            lead.segments.origin                        Segment Origin location Airport IATA-code
     * @apiParam {string{3}}            lead.segments.destination                   Segment Destination location Airport IATA-code
     * @apiParam {datetime{YYYY-MM-DD}} lead.segments.departure                     Segment Departure DateTime (format YYYY-MM-DD)
     * @apiParam {object}               lead.client                                 Client
     * @apiParam {string{20}}           lead.client.phone                           Client phone
     * @apiParam {int{2}=14-BOOK_FAILED, 15-ALTERNATIVE}   lead.status              Status
     * @apiParam {string{1}=E-ECONOMY, B-BUSINESS, F-FIRST, P-PREMIUM} lead.cabin   Cabin
     * @apiParam {int}                  lead.flight_id                              BO Flight ID
     *
     * @apiParamExample {json} Request-Example:
     *
     * {
     *      "lead": {
     *           "client": {
     *               "phone": "+37369333333"
     *           },
     *           "uid": "WD6q53PO3b",
     *           "status": 14,
     *           "source_code": "JIVOCH",
     *           "cabin": "E",
     *           "adults": 2,
     *           "children": 2,
     *           "infants": 2,
     *           "request_ip": "12.12.12.12",
     *           "discount_id": "123123",
     *           "user_agent": "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36",
     *           "flight_id": 12457,
     *           "segments": [
     *               {
     *                   "origin": "NYC",
     *                   "destination": "LON",
     *                   "departure": "2019-12-16"
     *               },
     *               {
     *                   "origin": "LON",
     *                   "destination": "NYC",
     *                   "departure": "2019-12-17"
     *               },
     *               {
     *                   "origin": "LON",
     *                   "destination": "NYC",
     *                   "departure": "2019-12-18"
     *               }
     *           ]
     *       }
     * }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     * {
     *       "status": 200,
     *       "message": "OK",
     *       "data": {
     *          "lead": {
     *               "id": 370949,
     *               "uid": "WD6q53PO3b",
     *               "gid": "63e1505f4a8a87e6651048e3e3eae4e1",
     *               "client_id": 1034,
     *               "client": {
     *                  "uuid": "35009a79-1a05-49d7-b876-2b884d0f825b"
     *                  "client_id": 331968,
     *                  "first_name": "Johann",
     *                  "middle_name": "Sebastian",
     *                  "last_name": "Bach",
     *                  "phones": [
     *                      "+13152572166"
     *                  ],
     *                  "emails": [
     *                      "example@test.com",
     *                      "bah@gmail.com"
     *                  ]
     *              }
     *           }
     *       }
     *       "request": {
     *           "lead": {
     *              "client": {
     *                   "phone": "+37369636963"
     *               },
     *               "uid": "WD6q53PO3b",
     *               "status": 14,
     *               "source_code": "JIVOCH",
     *               "cabin": "E",
     *               "adults": 2,
     *               "children": 2,
     *               "infants": 2,
     *               "request_ip": "12.12.12.12",
     *               "discount_id": "123123",
     *               "user_agent": "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.88 Safari/537.36",
     *               "flight_id": 12457,
     *               "segments": [
     *                   {
     *                       "origin": "NYC",
     *                       "destination": "LON",
     *                       "departure": "2019-12-16"
     *                   },
     *                   {
     *                       "origin": "LON",
     *                       "destination": "NYC",
     *                       "departure": "2019-12-17"
     *                   },
     *                   {
     *                       "origin": "LON",
     *                       "destination": "NYC",
     *                       "departure": "2019-12-18"
     *                   }
     *               ]
     *           }
     *       },
     *       "technical": {
     *           "action": "v2/lead/create",
     *           "response_id": 11930215,
     *           "request_dt": "2019-12-30 12:22:20",
     *           "response_dt": "2019-12-30 12:22:21",
     *           "execution_time": 0.055,
     *           "memory_usage": 1394416
     *       }
     * }
     *
     * @apiErrorExample {json} Error-Response (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     * {
     *       "status": 422,
     *       "message": "Validation error",
     *       "errors": {
     *           "children": [
     *               "Children must be no greater than 9."
     *           ],
     *           "segments[0][origin]": [
     *               "IATA (NY) not found."
     *           ],
     *           "segments[2][departure]": [
     *               "The format of Departure is invalid."
     *           ],
     *           "client[phone]": [
     *              "The format of Phone is invalid."
     *           ]
     *       },
     *       "code": 10301,
     *       "request": {
     *           ...
     *       },
     *       "technical": {
     *           ...
     *      }
     * }
     *
     * @apiErrorExample {json} Error-Response (400):
     *
     * HTTP/1.1 400 Bad Request
     * {
     *       "status": 400,
     *       "message": "Load data error",
     *       "errors": [
     *           "Not found Lead data on POST request"
     *       ],
     *       "code": 10300,
     *       "request": {
     *           ...
     *       },
     *       "technical": {
     *           ...
     *      }
     * }
     *
     * @apiErrorExample {json} Error-Response (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     * {
     *       "status": 422,
     *       "message": "Saving error",
     *       "errors": [
     *           "Saving error"
     *       ],
     *       "code": 10101,
     *       "request": {
     *           ...
     *       },
     *       "technical": {
     *           ...
     *      }
     * }
     *
     */
    public function actionCreate(): Response
    {
        $form = new LeadCreateForm();

        if (!$form->load(Yii::$app->request->post())) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found Lead data on POST request'),
                new CodeMessage(LeadCodeException::API_LEAD_NOT_FOUND_DATA_ON_REQUEST)
            );
        }

        if (!$form->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($form->getErrors()),
                new CodeMessage(LeadCodeException::API_LEAD_VALIDATE)
            );
        }

        try {
            $lead = $this->leadCreateHandler->handle($form);
        } catch (\Throwable $e) {
            return new ErrorResponse(
                new MessageMessage($e->getMessage()),
                new ErrorsMessage($e->getMessage()),
                new CodeMessage($e->getCode())
            );
        }

        return new SuccessResponse(
            new DataMessage(
                new LeadCreateMessage(
                    new LeadCreateValue([
                        'id' => $lead->id,
                        'uid' => $lead->uid,
                        'gid' => $lead->gid,
                        'client_id' => $lead->client_id,
                        'client' => [
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
                        ],
                    ])
                )
            )
        );
    }
}

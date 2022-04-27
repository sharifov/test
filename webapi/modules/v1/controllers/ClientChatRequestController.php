<?php

namespace webapi\modules\v1\controllers;

use common\components\jobs\clientChat\ClientChatFeedbackJob;
use common\components\jobs\clientChat\ClientChatRequestCreateJob;
use common\components\Metrics;
use common\models\ApiLog;
use common\models\Project;
use src\entities\cases\CaseCategory;
use src\helpers\app\AppHelper;
use src\helpers\setting\SettingHelper;
use src\model\clientChat\ClientChatTranslate;
use src\model\clientChat\entity\projectConfig\ClientChatProjectConfig;
use src\model\clientChat\entity\projectConfig\ProjectConfigApiResponseDto;
use src\model\clientChatChannel\entity\ClientChatChannel;
use src\model\clientChatForm\entity\ClientChatForm;
use src\model\clientChatForm\form\ClientChatFormApiForm;
use src\model\clientChatForm\helper\ClientChatFormTranslateHelper;
use src\model\clientChatRequest\entity\ClientChatRequest;
use src\model\clientChatRequest\repository\ClientChatRequestRepository;
use src\model\clientChatRequest\useCase\api\create\ClientChatRequestApiForm;
use src\model\clientChatRequest\useCase\api\create\ClientChatRequestFeedbackSubForm;
use src\model\clientChatRequest\useCase\api\create\ClientChatRequestService;
use src\repositories\NotFoundException;
use webapi\src\ApiCodeException;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\DataMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\Message;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\Response;
use webapi\src\response\SuccessResponse;
use Yii;
use yii\filters\HttpCache;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;
use webapi\src\Messages;

/**
 * Class ClientChatController
 * @package webapi\modules\v1\controllers
 *
 * @property ClientChatRequestService $clientChatRequestService
 * @property ClientChatRequestRepository $clientChatRequestRepository
 */
class ClientChatRequestController extends ApiBaseController
{
    private ClientChatRequestService $clientChatRequestService;
    /**
     * @var ClientChatRequestRepository
     */
    private ClientChatRequestRepository $clientChatRequestRepository;

    public function __construct(
        $id,
        $module,
        ClientChatRequestService $clientChatRequestService,
        ClientChatRequestRepository $clientChatRequestRepository,
        $config = []
    ) {
        $this->clientChatRequestService = $clientChatRequestService;
        parent::__construct($id, $module, $config);
        $this->clientChatRequestRepository = $clientChatRequestRepository;
    }

//    /**
//     * @return array
//     * @throws \yii\web\NotAcceptableHttpException
//     */
//    public function behaviors(): array
//    {
//        $behaviors = [
//            'HttpCache' => [
//                'class' => HttpCache::class,
//                'only' => ['projectConfig'],
//                'lastModified' => static function () {
//                    return strtotime(ClientChatProjectConfig::find()->max('ccpc_updated_dt'));
//                },
//            ],
//        ];
//        return ArrayHelper::merge(parent::behaviors(), $behaviors);
//    }

    /**
     * @return array
     */
    protected function verbs(): array
    {
        $verbs = parent::verbs();
        $verbs['projectConfig'] = ['GET'];
        return $verbs;
    }

    /**
     * @return Response
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     * @throws \yii\web\BadRequestHttpException
     * @throws \JsonException
     *
     *
     * @api {post} /v1/client-chat-request/create Client Chat Request
     * @apiVersion 0.1.0
     * @apiName ClientChatRequest
     * @apiGroup ClientChat
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization    Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     *
     *
     * @apiParamExample {json} Request-Example ROOM_CONNECTED:
     * {
            "event": "ROOM_CONNECTED",
            "data": {
                "rid": "d83ef2d3-30bf-4636-a2c6-7f5b4b0e81a4",
                "geo": {
                    "ip": "92.115.180.30",
                    "version": "IPv4",
                    "city": "Chisinau",
                    "region": "Chi\u0219in\u0103u Municipality",
                    "region_code": "CU",
                    "country": "MD",
                    "country_name": "Republic of Moldova",
                    "country_code": "MD",
                    "country_code_iso3": "MDA",
                    "country_capital": "Chisinau",
                    "country_tld": ".md",
                    "continent_code": "EU",
                    "in_eu": false,
                    "postal": "MD-2000",
                    "latitude": 47.0056,
                    "longitude": 28.8575,
                    "timezone": "Europe\/Chisinau",
                    "utc_offset": "+0300",
                    "country_calling_code": "+373",
                    "currency": "MDL",
                    "currency_name": "Leu",
                    "languages": "ro,ru,gag,tr",
                    "country_area": 33843,
                    "country_population": 3545883,
                    "asn": "AS8926",
                    "org": "Moldtelecom SA"
                },
                "visitor": {
                    "conversations": 0,
                    "lastAgentMessage": null,
                    "lastVisitorMessage": null,
                    "id": "fef46d63-8a30-4eec-89eb-62f1bfc0ffcd",
                    "username": "Test Usrename",
                    "name": "Test Name",
                    "uuid": "54d87707-bb54-46e3-9eca-8f776c7bcacf",
                    "project": "ovago",
                    "channel": "1",
                    "email": "test@techork.com",
                    "leadIds": [
                        234556,
                        357346
                    ],
                    "caseIds": [
                        345464634,
                        345634634
                    ]
                },
                "sources": {
                    "crossSystemXp": "123465.1"
                },
                "page": {
                    "url": "https:\/\/dev-ovago.travel-dev.com\/search\/WAS-FRA%2F2021-03-22%2F2021-03-28",
                    "title": "Air Ticket Booking - Find Cheap Flights and Airfare Deals - Ovago.com",
                    "referrer": "https:\/\/dev-ovago.travel-dev.com\/search\/WAS-FRA%2F2021-03-22%2F2021-03-28"
                },
                "system": {
                    "user_agent": "Mozilla\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/85.0.4183.102 Safari\/537.36",
                    "language": "en-US",
                    "resolution": "1920x1080"
                },
                "custom": {
                    "event": {
                        "eventName": "UPDATE",
                        "eventProps": []
                    }
                }
            }
     * }
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  {
     *     "status": 200
     *     "message": "Ok"
     *  }
     *
     * @apiErrorExample {json} Error-Response (400):
     *
     * HTTP/1.1 400 Bad Request
     * {
     *  "status":400,
     *  "message":"Some errors occurred while creating client chat request",
     *  "code":"13104",
     *  "errors":["Event is invalid."]
     * }
     *
     */
    public function actionCreate()
    {
        if (SettingHelper::isClientChatApiLogEnabled()) {
            $this->startApiLog($this->action->uniqueId);
        }

        if (!\Yii::$app->request->isPost) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('Not found POST request'),
                new CodeMessage(ApiCodeException::REQUEST_IS_NOT_POST)
            );
        }

        if (!\Yii::$app->request->post()) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('POST data request is empty'),
                new CodeMessage(ApiCodeException::POST_DATA_IS_EMPTY)
            );
        }

        $event = \Yii::$app->request->post('event');
        $data = \Yii::$app->request->post('data');

        if (!$event || !$data) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('Event or data is not provided'),
                new CodeMessage(ApiCodeException::EVENT_OR_DATA_IS_NOT_PROVIDED)
            );
        }

        $form = (new ClientChatRequestApiForm())->fillIn($event, $data);
        $metrics = \Yii::$container->get(Metrics::class);

        if ($form->validate()) {
            try {
                if ($requestEventCreate = ClientChatRequest::getEventCreatorByEventId($form->eventId)) {
                    $requestEventCreate->handle($form);
                }
            } catch (\RuntimeException | \DomainException | NotFoundException $e) {
                return $this->endApiLog(new ErrorResponse(
                    new StatusCodeMessage(400),
                    new MessageMessage($e->getMessage()),
                    new CodeMessage(ApiCodeException::CLIENT_CHAT_REQUEST_CREATE_FAILED)
                ));
            } catch (\Throwable $e) {
                \Yii::error(AppHelper::throwableFormatter($e), 'Api::ClientChatRequestController::actionCreate::Throwable');
                return $this->endApiLog(new ErrorResponse(
                    new StatusCodeMessage(500),
                    new MessageMessage('Internal Server Error'),
                    new CodeMessage(ApiCodeException::INTERNAL_SERVER_ERROR)
                ));
            }
            $metrics->serviceCounter('client_chat_request', ['type' => 'success', 'action' => 'create']);
            return $this->endApiLog(new SuccessResponse(
                new StatusCodeMessage(200),
                new MessageMessage('Ok'),
            ));
        }

        $metrics->serviceCounter('client_chat_request', ['type' => 'error', 'action' => 'create']);
        return $this->endApiLog(new ErrorResponse(
            new StatusCodeMessage(400),
            new MessageMessage('Some errors occurred while creating client chat request'),
            new ErrorsMessage($form->getErrorSummary(true)),
            new CodeMessage(ApiCodeException::NOT_FOUND_PROJECT_CURRENT_USER)
        ));
    }

    /**
     * @return Response
     * @throws NotFoundHttpException
     * @throws UnprocessableEntityHttpException
     * @throws \yii\web\BadRequestHttpException
     * @throws \JsonException
     *
     *
     * @api {post} /v1/client-chat-request/create-message Create Message
     * @apiVersion 0.1.0
     * @apiName ClientChatRequestCreateMessage
     * @apiGroup ClientChat
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization    Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     *
     *
     * @apiParamExample {json} Request-Example AGENT_UTTERED:
     * {
            "event": "AGENT_UTTERED",
            "data": {
                "id": "G6CBYkRYBotjaPPSu",
                "rid": "e19bf809-12c9-4981-89d0-da2f5d071890",
                "token": "56976e05-1916-44fb-a074-5a8d0358019b",
                "visitor": {
                    "conversations": 0,
                    "lastAgentMessage": null,
                    "lastVisitorMessage": null,
                    "id": "56976e05-1916-44fb-a074-5a8d0358019b",
                    "username": "guest-1219",
                    "phone": null,
                    "token": "56976e05-1916-44fb-a074-5a8d0358019b"
                },
                "agent": {
                    "name": "vadim_larsen_admin",
                    "username": "vadim_larsen_admin",
                    "email": "vadim.larsen@techork.com"
                },
                "msg": "test",
                "timestamp": 1602587182948,
                "u": {
                    "_id": "MszwfgYRGB9Tpw5Et",
                    "username": "vadim.larsen"
                },
                    "agentId": "MszwfgYRGB9Tpw5Et"
                }
     * }
     *
     * @apiParamExample {json} Request-Example GUEST_UTTERED with Attachment:
     * {
            "event": "GUEST_UTTERED",
            "data": {
                "id": "93ea7e9d-04cc-4f96-8bbf-d8b646113fd7",
                "rid": "88c395e3-fe19-4fe2-99dc-b0a1874efbdd",
                "token": "9728d3b4-5754-4339-9b0f-1c75edc727e9",
                "visitor": {
                    "conversations": 0,
                    "lastAgentMessage": null,
                    "lastVisitorMessage": null,
                    "id": "9728d3b4-5754-4339-9b0f-1c75edc727e9",
                    "name": "Henry Fonda",
                    "username": "guest-1220",
                    "phone": null,
                    "token": "9728d3b4-5754-4339-9b0f-1c75edc727e9"
                },
                "agent": {
                    "name": "bot",
                    "username": "bot",
                    "email": "bot@techork.com"
                },
                "msg": "Hi",
                "timestamp": 1602588445024,
                "u": {
                    "_id": "cYNGwXX6L8cN3eb2Q",
                    "username": "guest-1220",
                    "name": "Henry Fonda"
                }
            }
     * }
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  {
     *     "status": 200
     *     "message": "Ok"
     *  }
     *
     * @apiErrorExample {json} Error-Response (400):
     *
     * HTTP/1.1 400 Bad Request
     * {
     *  "status":400,
     *  "message":"Some errors occurred while creating client chat request",
     *  "code":"13104",
     *  "errors":["Event is invalid."]
     * }
     *
     */
    public function actionCreateMessage()
    {
        if (SettingHelper::isClientChatApiLogEnabled()) {
            $this->startApiLog($this->action->uniqueId);
        }

        if (!\Yii::$app->request->isPost) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('Not found POST request'),
                new CodeMessage(ApiCodeException::REQUEST_IS_NOT_POST)
            );
        }

        if (!\Yii::$app->request->post()) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('POST data request is empty'),
                new CodeMessage(ApiCodeException::POST_DATA_IS_EMPTY)
            );
        }

        $event = \Yii::$app->request->post('event');
        $data = \Yii::$app->request->post('data');

        if (!$event || !$data) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('Event or data is not provided'),
                new CodeMessage(ApiCodeException::EVENT_OR_DATA_IS_NOT_PROVIDED)
            );
        }

        $form = (new ClientChatRequestApiForm())->fillIn($event, $data);
        $metrics = \Yii::$container->get(Metrics::class);

        if ($form->validate()) {
            try {
                if ($requestEventCreate = ClientChatRequest::getEventCreatorByEventId($form->eventId)) {
                    $requestEventCreate->handle($form);
                }
            } catch (\RuntimeException | \DomainException | NotFoundException $e) {
                return $this->endApiLog(new ErrorResponse(
                    new StatusCodeMessage(400),
                    new MessageMessage($e->getMessage()),
                    new CodeMessage(ApiCodeException::CLIENT_CHAT_REQUEST_CREATE_FAILED)
                ));
            } catch (\Throwable $e) {
                \Yii::error(VarDumper::dumpAsString($e), 'Api::ClientChatRequestController::actionCreateMessage::Throwable');
                \Yii::error(VarDumper::dumpAsString($form->data), 'Api::ClientChatRequestController::actionCreateMessage::RequestData');
                return $this->endApiLog(new ErrorResponse(
                    new StatusCodeMessage(500),
                    new MessageMessage('Internal Server Error'),
                    new CodeMessage(ApiCodeException::INTERNAL_SERVER_ERROR)
                ));
            }

            $metrics->serviceCounter('client_chat_request', ['type' => 'success', 'action' => 'create_message']);

            return $this->endApiLog(new SuccessResponse(
                new StatusCodeMessage(200),
                new MessageMessage('Ok'),
            ));
        }

        $metrics->serviceCounter('client_chat_request', ['type' => 'error', 'action' => 'create_message']);

        return $this->endApiLog(new ErrorResponse(
            new StatusCodeMessage(400),
            new MessageMessage('Some errors occurred while creating client chat request'),
            new ErrorsMessage($form->getErrorSummary(true)),
            new CodeMessage(ApiCodeException::NOT_FOUND_PROJECT_CURRENT_USER)
        ));
    }

    /**
     * @api {get} /v1/client-chat-request/project-config Project Config
     * @apiVersion 0.1.0
     * @apiName ClientChatProjectConfig
     * @apiGroup ClientChat
     * @apiPermission Authorized User
     *
     * @apiParam {int} [project_id] Project ID
     * @apiParam {string{100}} [project_key] Project Key (Priority)
     * @apiParam {string{5}} [language_id] Language ID (ru-RU)
     * @apiParam {int=0, 1} [nocache] W/o cache
     *
     * @apiParamExample {get} Request-Example:
     * {
     *     "project_id": 1,
     *     "project_key": "ovago",
     *     "language_id": "ru-RU",
     *     "nocache": 1
     * }
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeader {string} Accept-Encoding
     * @apiHeader {string} If-Modified-Since  Format <code> day-name, day month year hour:minute:second GMT</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     * @apiHeaderExample {json} Header-Example (If-Modified-Since):
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate",
     *      "If-Modified-Since": "Mon, 23 Dec 2019 08:17:54 GMT",
     *  }
     *
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     *
     * {
     * "status": 200,
     * "message": "OK",
     * "data": {
     * "endpoint": "chatbot.travel-dev.com",
     * "enabled": true,
     * "project": "WOWFARE",
     * "projectKey": "wowfare",
     * "notificationSound": "https://cdn.travelinsides.com/npmstatic/assets/chime.mp3",
     * "theme": {
     * "theme": "linear-gradient(270deg, #0AAB99 0%, #1E71D1 100%)",
     * "primary": "#0C89DF",
     * "primaryDark": "#0066BA",
     * "accent": "#0C89DF",
     * "accentDark": "#0066BA"
     * },
     * "settings": {
     * },
     *
     * "channels": [
        {
            "id": 2,
            "name": "Channel 2",
            "priority": 1,
            "default": false,
            "enabled": true,
            "settings": {
                "max_dialog_count": 4,
                "feedback_rating_enabled": false,
                "feedback_message_enabled": true,
                "history_email_enabled": false,
                "history_download_enabled": true
            }
        },
        {
            "id": 3,
            "name": "Channel 11",
            "priority": 2,
            "default": true,
            "enabled": true,
            "settings": {
                "max_dialog_count": 1,
                "feedback_rating_enabled": true,
                "feedback_message_enabled": true,
                "history_email_enabled": true,
                "history_download_enabled": true
            }
        }
    ],
    "language_id": "ru-RU",
        "translations": {
            "connection_lost": {
                "title": "Connection Lost",
                "subtitle": "Trying to reconnect"
            },
            "waiting_for_response": "Waiting for response",
            "waiting_for_agent": "Waiting for an agent",
            "video_reply": "Video message",
            "audio_reply": "Audio message",
            "image_reply": "Image message",
            "new_message": "New message",
            "agent": "Agent",
            "textarea_placeholder": "Type a message...",
            "registration": {
                "title": "Welcome",
                "subtitle": "Be sure to leave a message",
                "name": "Name",
                "name_placeholder": "Your name",
                "email": "Email",
                "email_placeholder": "Your email",
                "department": "Department",
                "department_placeholder": "Choose a department",
                "start_chat": "Start chat"
            },
            "conversations": {
                "no_conversations": "No conversations yet",
                "no_archived_conversations": "No archived conversations yet",
                "history": "Conversation history",
                "active": "Active",
                "archived": "Archived Chats",
                "start_new": "New Chat"
            },
            "file_upload": {
                "file_too_big": "This file is too big. Max file size is {{size}}",
                "file_too_big_alt": "No archived conversations yetThis file is too large",
                "generic_error": "Failed to upload, please try again",
                "not_allowed": "This file type is not supported",
                "drop_file": "Drop file here to upload it",
                "upload_progress": "Uploading file..."
            },
            "department": {
                "sales": "Sales",
                "support": "Support",
                "exchange": "Exchange"
            }
        },
        "cache": true
    }
    }
     *
     *
     * @apiSuccessExample {json} Not Modified-Response (304):
     *
     * HTTP/1.1 304 Not Modified
     * Cache-Control: public, max-age=3600
     * Last-Modified: Mon, 23 Dec 2019 08:17:53 GMT
     *
     * @apiErrorExample {json} Error-Response (400):
     *
     * HTTP/1.1 400 Bad Request
     *   {
     * "status": 400,
     * "message": "Project Config not found",
     * "code": "13108",
     * "errors": []
     * }
     *
     */
    public function actionProjectConfig(): Response
    {
        if (!$this->request->isGet) {
            return new ErrorResponse(
                new StatusCodeMessage(405),
                new MessageMessage('Method not allowed.'),
            );
        }

        $projectId = (int)\Yii::$app->request->get('project_id');
        $projectKey = \Yii::$app->request->get('project_key');
        $noCache = (int) \Yii::$app->request->get('nocache', 0);

        if ($projectKey) {
            /** @var Project $project */
            $project = Project::find()->select(['id'])->where(['project_key' => $projectKey])->limit(1)->one();
            if (!$project) {
                return new ErrorResponse(
                    new StatusCodeMessage(404),
                    new MessageMessage('Not found Project by project key'),
                );
            }

            $projectId = $project->id;
        }

        $languageId = \Yii::$app->request->get('language_id');
        if ($languageId) {
            $languageId = substr($languageId, 0, 5);
        }

        $keyCache = ClientChatProjectConfig::getCacheKey($projectId, $languageId);

        if ($noCache) {
            Yii::$app->webApiCache->delete($keyCache);
        }

        $data = Yii::$app->webApiCache->get($keyCache);

        if ($data === false) {
            $projectConfig = ClientChatProjectConfig::findOne(['ccpc_project_id' => $projectId]);
            $projectChannels = ClientChatChannel::find()
                ->select(['ccc_frontend_name', 'ccc_id'])
                ->where(['ccc_project_id' => $projectId, 'ccc_disabled' => false])
                ->orderBy(['ccc_frontend_name' => SORT_ASC])
                ->indexBy('ccc_id')->asArray()->column();

            if ($projectConfig) {
                $data = ArrayHelper::toArray(new ProjectConfigApiResponseDto($projectConfig, $languageId));
                $data['language_id'] = $languageId;
                $data['translations'] = ClientChatTranslate::getTranslates($languageId);

                if ($projectChannels) {
                    $data['translations']['department'] = $projectChannels;
                }

                if ($data) {
                    Yii::$app->webApiCache->set($keyCache, $data, 60 * 60);
                }

                $data['cache'] = false;

                return new SuccessResponse(
                    new StatusCodeMessage(200),
                    new DataMessage($data)
                );
            }
        } else {
            $data['cache'] = true;
            return new SuccessResponse(
                new StatusCodeMessage(200),
                new DataMessage($data)
            );
        }


        return new ErrorResponse(
            new StatusCodeMessage(404),
            new MessageMessage('Project Config not found'),
            new CodeMessage(ApiCodeException::NOT_FOUND_PROJECT_CONFIG)
        );
    }

    /**
     * @api {post} /v1/client-chat-request/feedback Client Chat Feedback
     * @apiVersion 0.1.0
     * @apiName ClientChatFeedback
     * @apiGroup ClientChat
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization    Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParamExample {json} Request-Example LEAVE_FEEDBACK:
     * {
     *      "event": "LEAVE_FEEDBACK",
     *      "data": {
     *          "rid": "20a20989-4d26-42f4-9a1c-2948ce4c4d56",
     *          "comment": "Hello, this is my feedback",
     *          "rating": 4,
     *          "visitor": {
     *              "id": "1c1d90ff-5489-45f5-b19b-2181a65ce898",
     *              "project": "ovago"
     *          }
     *      }
     * }
     *
     * @apiSuccessExample Success-Response:
     *  HTTP/1.1 200 OK
     *  {
     *     "status": 200
     *     "message": "Ok"
     *  }
     *
     * @apiErrorExample {json} Error-Response (400):
     *
     * HTTP/1.1 400 Bad Request
     * {
     *  "status":400,
     *  "message":"Some errors occurred while creating client chat request",
     *  "code":"13104",
     *  "errors":["Event is invalid."]
     * }
     *
     * @return ErrorResponse|Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionFeedback()
    {
        if (SettingHelper::isClientChatApiLogEnabled()) {
            $this->startApiLog($this->action->uniqueId);
        }

        if (!\Yii::$app->request->isPost) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('Not found POST request'),
                new CodeMessage(ApiCodeException::REQUEST_IS_NOT_POST)
            );
        }

        if (!\Yii::$app->request->post()) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('POST data request is empty'),
                new CodeMessage(ApiCodeException::POST_DATA_IS_EMPTY)
            );
        }

        $event = \Yii::$app->request->post('event');
        $data = \Yii::$app->request->post('data');

        if (!$event || !$data) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('Event or data is not provided'),
                new CodeMessage(ApiCodeException::EVENT_OR_DATA_IS_NOT_PROVIDED)
            );
        }

        $form = (new ClientChatRequestApiForm())->fillIn($event, $data);
        if (!$form->validate()) {
            return $this->endApiLog(new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('Client Chat validate failed.'),
                new ErrorsMessage($form->getErrorSummary(true)),
                new CodeMessage(ApiCodeException::FAILED_FORM_VALIDATE)
            ));
        }

        $feedbackForm = (new ClientChatRequestFeedbackSubForm())->fillIn($data);
        if (!$feedbackForm->validate()) {
            return $this->endApiLog(new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('Feedback validate failed.'),
                new ErrorsMessage($feedbackForm->getErrorSummary(true)),
                new CodeMessage(ApiCodeException::FAILED_FORM_VALIDATE)
            ));
        }

        try {
            $clientChatRequest = ClientChatRequest::createByApi($form);
            $this->clientChatRequestRepository->save($clientChatRequest);
        } catch (\Throwable $e) {
            return $this->endApiLog(new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('Client Chat Request not saved.'),
                new ErrorsMessage($e->getMessage()),
                new CodeMessage(ApiCodeException::CLIENT_CHAT_REQUEST_CREATE_FAILED)
            ));
        }

        try {
            if (Yii::$app->params['settings']['enable_client_chat_job']) {
                $feedbackJob = new ClientChatFeedbackJob();
                $feedbackJob->rid = $feedbackForm->rid;
                $feedbackJob->comment = $feedbackForm->comment;
                $feedbackJob->rating = $feedbackForm->rating;

                if ($feedbackJobId = Yii::$app->queue_client_chat_job->priority(10)->push($feedbackJob)) {
                    $clientChatRequest->ccr_job_id = $feedbackJobId;
                    $clientChatRequest->save();
                    $resultMessage = 'Feedback added to queue (jobId: ' . $feedbackJobId . ')';
                } else {
                    throw new \Exception('Feedback not added to queue. ClientChatRequest ID : ' .
                        $clientChatRequest->ccr_rid);
                }
            } else {
                $clientChatFeedback = $this->clientChatRequestService->createOrUpdateFeedback(
                    $feedbackForm->rid,
                    $feedbackForm->comment,
                    $feedbackForm->rating
                );
                $resultMessage = 'Feedback saved (id: ' . $clientChatFeedback->ccf_id . ')';
            }
        } catch (\Throwable $e) {
            return $this->endApiLog(new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('Client Chat Feedback not saved.'),
                new ErrorsMessage($e->getMessage()),
                new CodeMessage(ApiCodeException::CLIENT_CHAT_FEEDBACK_CREATE_FAILED)
            ));
        }

        return $this->endApiLog(new SuccessResponse(
            new StatusCodeMessage(200),
            new MessageMessage($resultMessage ?? 'Ok'),
        ));
    }

    /**
     * @api {get} /v1/client-chat-request/chat-form Client Chat Form
     * @apiVersion 0.1.0
     * @apiName ClientChatForm
     * @apiGroup ClientChat
     * @apiPermission Authorized User
     *
     * @apiParam {string{100}} form_key Form Key
     * @apiParam {string{5}} language_id Language ID (en-US)
     * @apiParam {int=0, 1} [cache] Cache (not required, default eq 1)
     *
     * @apiParamExample {get} Request-Example:
     * {
     *     "form_key": "example_form",
     *     "language_id": "ru-RU",
     *     "cache": 1
     * }
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeader {string} Accept-Encoding
     * @apiHeader {string} If-Modified-Since  Format <code> day-name, day month year hour:minute:second GMT</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     * @apiHeaderExample {json} Header-Example (If-Modified-Since):
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate",
     *      "If-Modified-Since": "Mon, 23 Dec 2019 08:17:54 GMT",
     *  }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     * {
     * "status": 200,
     * "message": "OK",
     * "data": {
        "data_form": [
            {
                "type": "textarea",
                "name": "example_name",
                "className": "form-control",
                "label": "Please, describe problem",
                "required": true,
                "rows": 5
            },
            {
                "type": "select",
                "name": "destination",
                "className": "form-control",
                "label": "Куда летим?",
                "values": [
                    "label": "Амстердам",
                    "value": "AMS",
                    "selected": true
                ],
                [
                    "label": "Магадан",
                    "value": "GDX",
                    "selected": false
                ]
            },
            {
                "type": "button",
                "name": "button-123",
                "className": "btn-success btn",
                "label": "Submit"
            }
        ],
        "from_cache" : true
     }
     *
     * @apiErrorExample {json} Error-Response (400):
     *
     * HTTP/1.1 400 Bad Request
     *   {
     *     "status": 400,
     *     "message": "Validate failed",
     *     "code": "13110",
     *     "errors": []
     * }
     */
    public function actionChatForm()
    {
        if (SettingHelper::isClientChatApiLogEnabled()) {
            $this->startApiLog($this->action->uniqueId);
        }

        if (!$this->request->isGet) {
            return new ErrorResponse(
                new StatusCodeMessage(405),
                new MessageMessage('Method not allowed.'),
            );
        }

        $form = new ClientChatFormApiForm();
        if (!$form->load(Yii::$app->request->get())) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not loaded data from get request'),
                new CodeMessage(ApiCodeException::GET_DATA_NOT_LOADED)
            );
        }

        if (!$form->validate()) {
            return $this->endApiLog(new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('Validate failed'),
                new ErrorsMessage($form->getErrorSummary(true)),
                new CodeMessage(ApiCodeException::FAILED_FORM_VALIDATE)
            ));
        }

        try {
            $keyCache = ClientChatForm::getCacheKey($form->form_key, $form->language_id);

            if (!$form->cache) {
                Yii::$app->webApiCache->delete($keyCache);
            }

            $data = Yii::$app->webApiCache->get($keyCache);

            if ($data === false) {
                if (!$clientChatForm = ClientChatForm::findOne(['ccf_key' => $form->form_key])) {
                    throw new \Exception('Client Chat Form not found');
                }
                $data['data_form'] = ClientChatFormTranslateHelper::translateLabel($clientChatForm, $form->language_id);

                Yii::$app->webApiCache->set($keyCache, $data, ClientChatForm::CACHE_DURATION);

                $data['from_cache'] = false;
            } else {
                $data['from_cache'] = true;
            }
        } catch (\Throwable $e) {
            return $this->endApiLog(new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('Unexpected error'),
                new ErrorsMessage($e->getMessage()),
                new CodeMessage(ApiCodeException::UNEXPECTED_ERROR)
            ));
        }

        return $this->endApiLog(new SuccessResponse(
            new StatusCodeMessage(200),
            new MessageMessage('OK'),
            new DataMessage($data),
        ));
    }

    /**
     * @param ApiLog $apiLog
     * @param Response $response
     * @return Response
     */
    private function endApiLog(Response $response): Response
    {
        if ($this->apiLog) {
            $this->apiLog->endApiLog(ArrayHelper::toArray($response));
        }
        return $response;
    }
}

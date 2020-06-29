<?php

namespace webapi\modules\v1\controllers;

use common\models\ApiLog;
use DateTime;
use sales\helpers\app\AppHelper;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatMessage\ClientChatMessageRepository;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use sales\model\clientChatRequest\useCase\api\create\ClientChatRequestApiForm;
use sales\model\clientChatRequest\useCase\api\create\ClientChatRequestService;
use sales\repositories\NotFoundException;
use webapi\src\ApiCodeException;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\Response;
use webapi\src\response\SuccessResponse;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;

/**
 * Class ClientChatController
 * @package webapi\modules\v1\controllers
 *
 * @property ClientChatRequestService $clientChatRequestService
 */
class ClientChatRequestController extends ApiBaseController
{
	private const category = "ClientChatRequestController";

    private ClientChatRequestService $clientChatRequestService;

	public function __construct($id, $module, ClientChatRequestService $clientChatRequestService, $config = [])
	{
		$this->clientChatRequestService = $clientChatRequestService;
		parent::__construct($id, $module, $config);
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
	 * @apiParamExample {json} Request-Example GUEST_CONNECTED:
	 * {
			"event": "GUEST_CONNECTED",
			"data": {
				"rid": "292a9961-asdas-4d66-bhnjm-sdvsdv",
				"channel": "livechat-channel",
				"name": "Test 45",
				"email": "test+45@mail.com",
				"ip": "127.0.0.1",
				"project": 'OVAGO',
	 			"department": "department id optional"
			}
	 * }
	 *
	 * @apiParamExample {json} Request-Example ROOM_CONNECTED:
	 * {
			"event": 'ROOM_CONNECTED',
			"data": {
				"rid": '9e563c67-fe10-42d5-a664-6e30d2974201',
				"visitor": { "_id": 'pnjNRHsnnWXhW5LBn', "username": 'guest-81' }
			}
	 * }
	 *
	 * @apiSuccessExample Success-Response:
	 *  HTTP/1.1 200 OK
	 *  {
	 *     "status": 200
	 *     "message": "Client chat request created"
	 *  }
	 *
	 * @apiErrorExample {json} Error-Response (400):
	 *
	 * HTTP/1.1 400 Bad Request
	 * {
	 * 	"status":400,
	 * 	"message":"Some errors occurred while creating client chat request",
	 * 	"code":"13104",
	 * 	"errors":["Event is invalid."]
	 * }
	 *
	 */
	public function actionCreate()
	{
		$apiLog = $this->startApiLog($this->action->uniqueId);

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

		if ($form->validate()) {
		    if ($form->isMessageEvent()) {
                try {
                    $this->saveMessage($form);
                    return $this->endApiLog($apiLog, new SuccessResponse(
                        new StatusCodeMessage(200),
                        new MessageMessage('OK'),
                    ));
                }catch (\Throwable $e) {
                    \Yii::error("failed to store client chat message". AppHelper::throwableFormatter($e), self::category);
                    return new ErrorResponse(
                        new StatusCodeMessage(500),
                        new MessageMessage('Failed to store client chat message'),
                        new CodeMessage(ApiCodeException::INTERNAL_SERVER_ERROR)
                    );
                }
            } else {
                try {
                    $transaction = \Yii::$app->db->beginTransaction();

                    $this->clientChatRequestService->create($form);

                    $transaction->commit();
                } catch (\RuntimeException | \DomainException | NotFoundException $e) {
                    $transaction->rollBack();
                    return $this->endApiLog($apiLog, new ErrorResponse(
                        new StatusCodeMessage(400),
                        new MessageMessage($e->getMessage()),
                        new CodeMessage(ApiCodeException::CLIENT_CHAT_REQUEST_CREATE_FAILED)
                    ));
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    \Yii::error($e->getMessage() . '; In File: ' . $e->getFile() . '; On Line: ' . $e->getLine(), 'Api::ClientChatRequestController::actionCreate::Throwable');
                    return $this->endApiLog($apiLog, new ErrorResponse(
                        new StatusCodeMessage(500),
                        new MessageMessage('Internal Server Error'),
                        new CodeMessage(ApiCodeException::INTERNAL_SERVER_ERROR)
                    ));
                }

			return $this->endApiLog($apiLog, new SuccessResponse(
				new StatusCodeMessage(200),
				new MessageMessage('Client chat request created successfully'),
			));
		}}

		return $this->endApiLog($apiLog, new ErrorResponse(
			new StatusCodeMessage(400),
			new MessageMessage('Some errors occurred while creating client chat request'),
			new ErrorsMessage($form->getErrorSummary(true)),
			new CodeMessage(ApiCodeException::NOT_FOUND_PROJECT_CURRENT_USER)
		));
	}

	private function endApiLog(ApiLog $apiLog, Response $response): Response
	{
		$apiLog->endApiLog(ArrayHelper::toArray($response));
		return $response;
	}

    /**
     * Save message into db
     * @param ClientChatRequestApiForm $form
     */
    public function saveMessage(ClientChatRequestApiForm $form): void
    {
        $message = self::messageFromData($form);
        $clientChat = ClientChatRepository::findByRid($message->ccm_rid);
        if (is_null($clientChat)) {
            \Yii::error("unable to find client chat by rid: ". $message->ccm_rid, self::category);
            return;
        }

        $message->ccm_client_id = $clientChat->cch_client_id;
        //if agent message fill also agent id
        if ($form->isAgentUttered()) {
            $message->ccm_user_id = $clientChat->cch_owner_user_id;
        }

        ClientChatMessageRepository::saveMessage($message);
        return;
    }

    /**
     * @param ClientChatRequestApiForm $formData
     * @return ClientChatMessage
     */
    public static function messageFromData(ClientChatRequestApiForm $formData) : ClientChatMessage {
        $message = new ClientChatMessage();
        $message->ccm_rid = $formData->data['rid'];
        $date = new DateTime();
        $date->setTimestamp($formData->data['ts']['$date']/1000);
        $message->ccm_sent_dt = $date->format('Y-m-d H:i:s');
        $message->ccm_body = $formData->data;

        if (array_key_exists('file', $formData->data)) {
            $message->ccm_has_attachment = 1;
        }

        return $message;
    }
}
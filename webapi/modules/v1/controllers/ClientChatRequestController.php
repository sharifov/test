<?php

namespace webapi\modules\v1\controllers;

use sales\model\clientChatRequest\entity\ClientChatRequest;
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
			try {
				$transaction = \Yii::$app->db->beginTransaction();

				$this->clientChatRequestService->create($form);

				$transaction->commit();
			} catch (\RuntimeException $e) {
				$transaction->rollBack();
				return new ErrorResponse(
					new StatusCodeMessage(400),
					new MessageMessage($e->getMessage()),
					new CodeMessage(ApiCodeException::CLIENT_CHAT_REQUEST_CREATE_FAILED)
				);
			} catch (\Throwable $e) {
				$transaction->rollBack();
				\Yii::error($e->getMessage() . '; In File: ' . $e->getFile() . '; On Line: ' . $e->getLine(), 'Api::ClientChatRequestController::actionCreate::Throwable');
				return new ErrorResponse(
					new StatusCodeMessage(500),
					new MessageMessage('Internal Server Error'),
					new CodeMessage(ApiCodeException::INTERNAL_SERVER_ERROR)
				);
			}

			return new SuccessResponse(
				new StatusCodeMessage(200),
				new MessageMessage('Client chat request created successfully'),
			);
		}

		return new ErrorResponse(
			new StatusCodeMessage(400),
			new MessageMessage('Some errors occurred while creating client chat request'),
			new ErrorsMessage($form->getErrorSummary(true)),
			new CodeMessage(ApiCodeException::NOT_FOUND_PROJECT_CURRENT_USER)
		);
	}
}
<?php

namespace webapi\modules\v1\controllers;

use common\models\ApiLog;
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
	 *     "message": "Ok"
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
				new MessageMessage('Ok'),
			));
		}

		return $this->endApiLog($apiLog, new ErrorResponse(
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
	 * @api {post} /v1/client-chat-request/create-message Client Chat Request Create Message
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
	 * @apiParamExample {json} Request-Example GUEST_UTTERED:
	 * {
			"event": "GUEST_UTTERED",
			"data": {
				"_id": "XipJ2fqumzr3n3Nhh",
				"rid": "292a9961-asdas-4d66-bhnjm-sdgadg",
				"msg": "hello",
				"token": "292a9961-asdas-4d66-bhnjm-sdvsdu",
				"alias": "jora",
				"ts": {
					"$date": 1592924833106
				},
				"u": {
					"_id": "pvS87aeQxfqgnzsFW",
					"username": "guest-124",
					"name": "jora"
				},
				"groupable": false,
				"mentions": [],
				"channels": [],
				"_updatedAt": {
					"$date": 1592924833113
				}
			}
	 * }
	 *
	 * @apiParamExample {json} Request-Example GUEST_UTTERED with Attachment:
	 * {
			"event": "GUEST_UTTERED",
			"data": {
				"_id": "XipJ2fqumzr3n3Nhh",
				"rid": "292a9961-asdas-4d66-bhnjm-sdgadg",
				"msg": "hello",
				"token": "292a9961-asdas-4d66-bhnjm-sdvsdu",
				"alias": "jora",
				"ts": {
					"$date": 1592924833106
				},
				"file": {
					"_id": "EKSp7qfb7LqQxjA3N",
					"name": "469_5263_9274dc35-1390-4b95-9767-fa4b71adc7a2-c26e70f8-0eff-4558-8005-73a699c7d7f8.mp4",
					"type": "video/mp4"
				},
				"attachments": [
					{
						"title": "469_5263_9274dc35-1390-4b95-9767-fa4b71adc7a2-c26e70f8-0eff-4558-8005-73a699c7d7f8.mp4",
						"type": "file",
						"title_link": "/file-upload/EKSp7qfb7LqQxjA3N/469_5263_9274dc35-1390-4b95-9767-fa4b71adc7a2-c26e70f8-0eff-4558-8005-73a699c7d7f8.mp4",
						"title_link_download": true,
						"video_url": "/file-upload/EKSp7qfb7LqQxjA3N/469_5263_9274dc35-1390-4b95-9767-fa4b71adc7a2-c26e70f8-0eff-4558-8005-73a699c7d7f8.mp4",
						"video_type": "video/mp4",
						"video_size": 5276
					}
				],
				"u": {
					"_id": "pvS87aeQxfqgnzsFW",
					"username": "guest-124",
					"name": "jora"
				},
				"groupable": false,
				"mentions": [],
				"channels": [],
				"_updatedAt": {
					"$date": 1592924833113
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
	 * 	"status":400,
	 * 	"message":"Some errors occurred while creating client chat request",
	 * 	"code":"13104",
	 * 	"errors":["Event is invalid."]
	 * }
	 *
	 */
	public function actionCreateMessage()
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
				$this->clientChatRequestService->createMessage($form);
			} catch (\RuntimeException | \DomainException | NotFoundException $e) {
				return new ErrorResponse(
					new StatusCodeMessage(400),
					new MessageMessage($e->getMessage()),
					new CodeMessage(ApiCodeException::CLIENT_CHAT_REQUEST_CREATE_FAILED)
				);
			} catch (\Throwable $e) {
				\Yii::error($e->getMessage() . '; In File: ' . $e->getFile() . '; On Line: ' . $e->getLine(), 'Api::ClientChatRequestController::actionCreate::Throwable');
				return  new ErrorResponse(
					new StatusCodeMessage(500),
					new MessageMessage('Internal Server Error'),
					new CodeMessage(ApiCodeException::INTERNAL_SERVER_ERROR)
				);
			}

			return  new SuccessResponse(
				new StatusCodeMessage(200),
				new MessageMessage('Ok'),
			);
		}

		return new ErrorResponse(
			new StatusCodeMessage(400),
			new MessageMessage('Some errors occurred while creating client chat request'),
			new ErrorsMessage($form->getErrorSummary(true)),
			new CodeMessage(ApiCodeException::NOT_FOUND_PROJECT_CURRENT_USER)
		);
	}

	private function endApiLog(ApiLog $apiLog, Response $response): Response
	{
		$apiLog->endApiLog(ArrayHelper::toArray($response));
		return $response;
	}
}
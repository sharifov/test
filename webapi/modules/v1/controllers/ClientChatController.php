<?php

namespace webapi\modules\v1\controllers;

use common\models\ApiLog;
use sales\helpers\app\AppHelper;
use sales\model\clientChatForm\form\ClientChatSubscribeForm;
use sales\model\clientChatForm\form\ClientChatUnsubscribeForm;
use sales\model\visitorSubscription\repository\VisitorSubscriptionRepository;
use sales\model\visitorSubscription\service\VisitorSubscriptionApiManageService;
use sales\repositories\NotFoundException;
use webapi\src\ApiCodeException;
use webapi\src\Messages;
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

/***
 * Class ClientChatController
 * @package webapi\modules\v1\controllers
 *
 * @property-read VisitorSubscriptionApiManageService $service
 * @property-read VisitorSubscriptionRepository $subscriptionRepository
 */
class ClientChatController extends ApiBaseController
{
    /**
     * @var VisitorSubscriptionApiManageService
     */
    private VisitorSubscriptionApiManageService $service;
    /**
     * @var VisitorSubscriptionRepository
     */
    private VisitorSubscriptionRepository $subscriptionRepository;

    public function __construct(
        $id,
        $module,
        VisitorSubscriptionApiManageService $service,
        VisitorSubscriptionRepository $subscriptionRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->service = $service;
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * @return Response
     *
     * @api {post} /v1/client-chat/subscribe Client Chat Subscribe
     * @apiVersion 0.1.0
     * @apiName ClientChat Subscribe
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
     * @apiParam {string}                       chat_visitor_id         Chat visitor Id
     * @apiParam {string{max 100}}              subscription_uid        Subscription Unique id <code>Required</code>
     * @apiParam {string}                       chat_room_id            Chat room id
     * @apiParam {string}                       expired_date            Subscription expiration date <code>format yyyy-mm-dd</code>
     *
     *
     * @apiParamExample {json} Request-Example Flizzard Subscription:
     * {
            "chat_visitor_id": "5779293e-dd0f-476f-b0aa-bbbb",
            "subscription_uid": "aksdjAICl5mm590vml",
            "chat_room_id": "9e06ff33-a3b3-4fa0-aa88-asdw2f45gted54yh",
            "expired_date": "2021-10-25",
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
            "status": 400,
            "message": "Some errors occurred while creating client chat request",
            "errors": [
                "Visitor subscription saving error: Subscription uid with type has already been taken"
            ],
            "code": "13101"
        }
     *
     */
    public function actionSubscribe()
    {
        $apiLog = $this->startApiLog($this->action->uniqueId);

        $form = new ClientChatSubscribeForm();

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

        if (!$form->load(\Yii::$app->request->post())) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not loaded data from get request'),
                new CodeMessage(ApiCodeException::GET_DATA_NOT_LOADED)
            );
        }

        if ($form->validate()) {
            try {
                $this->service->createFlizzardSubscription($form);

                return $this->endApiLog($apiLog, new SuccessResponse(
                    new StatusCodeMessage(200),
                    new MessageMessage('Ok'),
                ));
            } catch (\RuntimeException $e) {
                $form->addError('general', $e->getMessage());
            } catch (\Throwable $e) {
                \Yii::error(AppHelper::throwableLog($e, true), 'API::v1::ClientChatController::actionSubscribe::Throwable');
            }
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
     *
     * @api {post} /v1/client-chat/unsubscribe Client Chat Unsubscribe
     * @apiVersion 0.1.0
     * @apiName ClientChat Unsubscribe
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
     * @apiParam {string{max 100}}      subscription_uid     Subscription Unique id <code>Required</code>
     *
     *
     * @apiParamExample {json} Request-Example:
     * {
            "subscription_uid": "asgfaposj-34ffd-t34fge",
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
            "status": 400,
            "message": "Some errors occurred while creating client chat request",
            "errors": [
                "Subscription not found by uid: asgfaposj-34ffd-t34fge"
            ],
            "code": "13101"
        }
     *
     */
    public function actionUnsubscribe()
    {
        $apiLog = $this->startApiLog($this->action->uniqueId);

        $form = new ClientChatUnsubscribeForm();

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

        if (!$form->load(\Yii::$app->request->post())) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not loaded data from get request'),
                new CodeMessage(ApiCodeException::GET_DATA_NOT_LOADED)
            );
        }

        if ($form->validate()) {
            try {
                $subscription = $this->subscriptionRepository->findByUid($form->subscription_uid);
                $subscription->disabled();
                $this->subscriptionRepository->save($subscription);

                return $this->endApiLog($apiLog, new SuccessResponse(
                    new StatusCodeMessage(200),
                    new MessageMessage('Ok'),
                ));
            } catch (\RuntimeException | NotFoundException $e) {
                $form->addError('general', $e->getMessage());
            } catch (\Throwable $e) {
                \Yii::error(AppHelper::throwableLog($e, true), 'API::v1::ClientChatController::actionSubscribe::Throwable');
            }
        }

        return $this->endApiLog($apiLog, new ErrorResponse(
            new StatusCodeMessage(400),
            new MessageMessage('Some errors occurred while creating client chat request'),
            new ErrorsMessage($form->getErrorSummary(true)),
            new CodeMessage(ApiCodeException::NOT_FOUND_PROJECT_CURRENT_USER)
        ));
    }

    /**
     * @param ApiLog $apiLog
     * @param Response $response
     * @return Response
     */
    private function endApiLog(ApiLog $apiLog, Response $response): Response
    {
        $apiLog->endApiLog(ArrayHelper::toArray($response));
        return $response;
    }
}

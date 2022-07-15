<?php

namespace webapi\modules\v2\controllers;

use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use src\entities\cases\CaseEventLog;
use src\helpers\app\AppHelper;
use src\repositories\cases\CasesRepository;
use src\repositories\NotFoundException;
use webapi\src\forms\boWebhook\BoWebhookForm;
use webapi\src\forms\boWebhook\ReprotectionUpdateForm;
use webapi\src\jobs\BoWebhookHandleJob;
use webapi\src\logger\ApiLogger;
use webapi\src\logger\behaviors\SimpleLoggerBehavior;
use webapi\src\logger\behaviors\TechnicalInfoBehavior;
use webapi\src\Messages;
use webapi\src\request\BoWebhook;
use webapi\src\response\behaviors\RequestBehavior;
use webapi\src\response\behaviors\ResponseStatusCodeBehavior;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\DataMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\Message;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\SuccessResponse;
use Yii;
use src\repositories\product\ProductQuoteRepository;
use src\services\cases\CasesManageService;

class BoController extends BaseController
{
    /**
     * @var ProductQuoteRepository
     */
    private ProductQuoteRepository $productQuoteRepository;

    /**
     * @param $id
     * @param $module
     * @param ApiLogger $logger
     * @param ProductQuoteRepository $productQuoteRepository
     * @param array $config
     */
    public function __construct(
        $id,
        $module,
        ApiLogger $logger,
        ProductQuoteRepository $productQuoteRepository,
        $config = []
    ) {
        $this->productQuoteRepository = $productQuoteRepository;
        parent::__construct($id, $module, $logger, $config);
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['logger'] = [
            'class' => SimpleLoggerBehavior::class,
            'except' => [],
        ];
        $behaviors['request'] = [
            'class' => RequestBehavior::class,
            'except' => ['wh'],
        ];
        $behaviors['responseStatusCode'] = [
            'class' => ResponseStatusCodeBehavior::class,
            'except' => ['wh'],
        ];
        $behaviors['technical'] = [
            'class' => TechnicalInfoBehavior::class,
            'except' => ['wh'],
        ];
        return $behaviors;
    }

    /**
     * @api {post} /v2/bo/wh  WebHook Reprotection Update (BackOffice)
     * @apiVersion 0.1.0
     * @apiName BackOffice WebHook Reprotection Update
     * @apiGroup WebHooks Incoming
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{30}=reprotection_update}           type                          Message Type action
     * @apiParam {array[]}              data                          Any Data
     * @apiParam {string{8}}               data.booking_id               Booking Id
     * @apiParam {string{20}}               data.project_key            Project Key ("ovago", "hop2")
     * @apiParam {string{32}}               data.reprotection_quote_gid   Reprotection quote GID
     * @apiParam {string{20}=Processing, Exchanged, Canceled}               data.status               Exchang status
     *
     * @apiParamExample {json} Request-Example Reprotection Update:
     *  {
     *      "type": "reprotection_update",
     *      "data": {
     *          "booking_id": "C4RB44",
     *          "project_key": "ovago",
     *          "reprotection_quote_gid": "4569a42c916c811e2033142d8ae54179",
     *          "status": "Exchanged" // allowed values Processing, Exchanged, Canceled
     *      }
     *  }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     * {
     *     "status": 200,
     *     "message": "OK",
     *      "data": {
     *          "success": true
     *      }
     * }
     *
     * @apiErrorExample {json} Error-Response:
     * HTTP/1.1 400 Bad Request
     * {
     *      "status": 400,
     *      "message": "Load data error",
     *      "errors": [
     *          "Not found data on POST request"
     *       ]
     * }
     */

    /**
     * @api {post} /v2/bo/wh  WebHook Flight Refund (BackOffice)
     * @apiVersion 0.1.0
     * @apiName BackOffice WebHook Flight Refund
     * @apiGroup WebHooks Incoming
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{30}=flight_refund}           type                          Message Type action
     * @apiParam {array[]}              data                          Any Data
     * @apiParam {string{8}}               data.booking_id               Booking Id
     * @apiParam {string{20}}               data.project_key            Project Key ("ovago", "hop2")
     * @apiParam {string{20}=Processing,Refunded,Canceled}               data.status               Refund status
     *
     *
     * @apiParamExample {json} Request-Example Flight Refund:
     *  {
     *      "type": "flight_refund",
     *      "data": {
     *          "booking_id": "C4RB44",
     *          "project_key": "ovago",
     *          "status": "Refunded", // allowed values Processing, Refunded, Canceled
     *      }
     *  }
     *
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     * {
     *     "status": 200,
     *     "message": "OK",
     *      "data": {
     *          "success": true
     *      }
     * }
     *
     * @apiErrorExample {json} Error-Response:
     * HTTP/1.1 400 Bad Request
     * {
     *      "status": 400,
     *      "message": "Load data error",
     *      "errors": [
     *          "Not found data on POST request"
     *       ]
     * }
     */

    /**
     * @api {post} /v2/bo/wh  WebHook Voluntary Flight Refund (BackOffice)
     * @apiVersion 0.1.0
     * @apiName BackOffice WebHook Voluntary Flight Refund
     * @apiGroup WebHooks Incoming
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{30}=voluntary_flight_refund}           type                          Message Type action
     * @apiParam {array[]}              data                          Any Data
     * @apiParam {string{8}}               data.booking_id               Booking Id
     * @apiParam {string{20}}               data.project_key            Project Key ("ovago", "hop2")
     * @apiParam {string{20}=Processing,Refunded,Canceled}               data.status               Refund status
     * @apiParam {string{32}}               data.orderId              Refund Order Id
     *
     *
     * @apiParamExample {json} Request-Example Voluntary Flight Refund:
     *  {
     *      "type": "voluntary_flight_refund",
     *      "data": {
     *          "booking_id": "C4RB44",
     *          "project_key": "ovago",
     *          "status": "Refunded", // allowed values Processing, Refunded, Canceled
     *          "orderId": "RT-SHCN37D" // OTA Refund order id
     *      }
     *  }
     *
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     * {
     *     "status": 200,
     *     "message": "OK",
     *      "data": {
     *          "success": true
     *      }
     * }
     *
     * @apiErrorExample {json} Error-Response:
     * HTTP/1.1 400 Bad Request
     * {
     *      "status": 400,
     *      "message": "Load data error",
     *      "errors": [
     *          "Not found data on POST request"
     *       ]
     * }
     */

    /**
     * @api {post} /v2/bo/wh  WebHook Voluntary Flight Exchange (BackOffice)
     * @apiVersion 0.1.0
     * @apiName BackOffice WebHook Voluntary Flight Exchange
     * @apiGroup WebHooks Incoming
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{30}=flight_exchange}           type                          Type action
     * @apiParam {array[]}              data                          Any Data
     * @apiParam {string{8}}               data.booking_id               Booking Id
     * @apiParam {string{20}}               data.project_key            Project Key (ovago, hop2)
     * @apiParam {string{20}=Processing,Exchanged,Canceled}               data.status   Exchange status
     * @apiParam {string{32}}               [data.gid]            GID (08671a147555ba45f74306bc9d95e41a)
     *
     *
     * @apiParamExample {json} Request-Example Voluntary Flight Exchange:
     *  {
     *      "type": "flight_exchange",
     *      "data": {
     *          "booking_id": "C4RB44",
     *          "project_key": "ovago",
     *          "status": "Exchanged", // allowed values Pending, Processing, Exchanged, Canceled
     *      }
     *  }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     * {
     *     "status": 200,
     *     "message": "OK",
     *      "data": {
     *          "success": true
     *      }
     * }
     *
     * @apiErrorExample {json} Error-Response:
     * HTTP/1.1 400 Bad Request
     * {
     *      "status": 400,
     *      "message": "Load data error",
     *      "errors": [
     *          "Not found data on POST request"
     *       ]
     * }
     */


    public function actionWh()
    {
        $form = new BoWebhookForm();

        try {
            $post = Yii::$app->request->post();
        } catch (\Throwable $throwable) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage($throwable->getMessage()),
            );
        }

        if (!$form->load($post)) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found data on POST request'),
            );
        }
        if (!$form->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($form->getErrors()),
            );
        }

        $dataForm = BoWebhook::getFormByType($form->typeId);
        if (!$dataForm) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage('Unknown Type'),
            );
        }
        if (!$dataForm->load($form->data) || !$dataForm->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($dataForm->getErrors()),
            );
        }

        try {
            $job = new BoWebhookHandleJob();
            $job->form = $dataForm;
            $job->requestTypeId = $form->typeId;
            Yii::$app->queue_job->priority(100)->push($job);

            return new SuccessResponse(new DataMessage([
                'success' => true,
            ]));
        } catch (NotFoundException | \RuntimeException $e) {
            return new ErrorResponse(
                new StatusCodeMessage(422),
                new MessageMessage($e->getMessage()),
                new ErrorsMessage($e->getMessage()),
                new CodeMessage($e->getCode())
            );
        } catch (\Throwable $e) {
            \Yii::error(AppHelper::throwableLog($e), 'API:BoController:actionWh:Throwable');
            return new ErrorResponse(
                new StatusCodeMessage(500),
                new MessageMessage('Internal Server Error'),
                new CodeMessage($e->getCode())
            );
        }
    }
}

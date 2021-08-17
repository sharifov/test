<?php

namespace webapi\modules\v2\controllers;

use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use sales\entities\cases\CaseEventLog;
use sales\helpers\app\AppHelper;
use sales\repositories\cases\CasesRepository;
use sales\repositories\NotFoundException;
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
use sales\repositories\product\ProductQuoteRepository;
use sales\services\cases\CasesManageService;

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
     * @api {post} /v2/bo/wh BO Webhook
     * @apiVersion 0.1.0
     * @apiName BO Webhook
     * @apiGroup Webhook
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{30}=reprotection_update,flight_refund_update}           type                          Type of action on reprotection
     * @apiParam {array[]}              data                          Any Data from BO
     * @apiParam {string}               data.booking_id               Booking Id
     * @apiParam {string}               [data.project_key]            Project Key Ex: (ovago, hop2)
     * @apiParam {string}               data.reprotection_quote_gid   Reprotection quote gid
     *
     * @apiParamExample {json} Request-Example Reprotection Update:
     *  {
     *      "type": "reprotection_update",
     *      "data": {
     *          "booking_id": "C4RB44",
     *          "project_key": "ovago",
     *          "reprotection_quote_gid": "4569a42c916c811e2033142d8ae54179"
     *      }
     *  }
     *
     * @apiParamExample {json} Request-Example Flight Refund Update:
     *  {
     *      "type": "flight_refund_update",
     *      "data": {
     *          "booking_id": "C4RB44",
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

        if (!$form->load(Yii::$app->request->post())) {
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

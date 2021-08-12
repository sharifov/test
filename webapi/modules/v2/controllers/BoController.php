<?php

namespace webapi\modules\v2\controllers;

use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use sales\helpers\app\AppHelper;
use sales\repositories\cases\CasesRepository;
use sales\repositories\NotFoundException;
use webapi\src\logger\ApiLogger;
use webapi\src\logger\behaviors\SimpleLoggerBehavior;
use webapi\src\logger\behaviors\TechnicalInfoBehavior;
use webapi\src\Messages;
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
use modules\flight\src\useCases\reprotectionUpdate\form\ReprotectionUpdateForm;

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
            'except' => ['wh'],
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
     * @api {post} /v2/bo/wh Reprotection Update from BO
     * @apiVersion 0.1.0
     * @apiName ReProtection Update
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
     * @apiParam {string{30}}           type                        Type of action on reprotection
     * @apiParam {array[]}              data                        Any Data from BO
     * @apiParam {string}               data.booking_id             Booking Id
     * @apiParam {string}               data.project_key            Project Key Ex: (ovago, hop2)
     * @apiParam {string}               data.reprotection_quote_gid            Project Key Ex: (ovago, hop2)
     *
     * @apiParamExample {json} Request-Example:
     *  {
     *      "type": "reprotection-update",
     *      "data": {
     *          "booking_id": "C4RB44",
     *          "project_key": "ovago",
     *          "reprotection_quote_gid": "4569a42c916c811e2033142d8ae54179"
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
        $form = new ReprotectionUpdateForm();
        $data = Yii::$app->request->post();

        if (!$form->load($data)) {
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

        try {
            $productQuote = $this->productQuoteRepository->findByGidFlightProductQuote($form->data['reprotection_quote_gid']);
            if ($productQuote->isInProgress()) {
                $productQuote->booked();
                $repo = Yii::createObject(ProductQuoteRepository::class);
                $repo->save($productQuote);

                $pqChange = $productQuote->relateParent->productQuoteLastChange;
                $pqChange->pqc_status_id = ProductQuoteChangeStatus::COMPLETE;
                $pqChange->save();

                $case = $productQuote->relateParent->productQuoteLastChange->pqcCase;
                $case->cs_is_automate = false;
                Yii::createObject(CasesManageService::class)->solved($case, null, 'system:Bo Webhook');
                $caseRepo = Yii::createObject(CasesRepository::class);
                $caseRepo->save($case);
            }

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

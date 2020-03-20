<?php

namespace webapi\modules\v2\controllers;

use sales\entities\cases\Cases;
use sales\model\cases\CaseCodeException;
use sales\model\cases\useCases\cases\api\create\CreateForm;
use sales\model\cases\useCases\cases\api\create\Handler;
use sales\services\cases\CasesSaleService;
use webapi\src\ApiCodeException;
use webapi\src\logger\ApiLogger;
use webapi\src\Messages;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\DataMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\Message;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\SuccessResponse;
use Yii;
use webapi\src\response\Response;
use yii\helpers\VarDumper;

/**
 * Class CasesController
 *
 * @property Handler $createHandler
 * @property CasesSaleService $casesSaleService
 */
class CasesController extends BaseController
{
    private $createHandler;
    private $casesSaleService;

    public function __construct(
        $id,
        $module,
        ApiLogger $logger,
        Handler $createHandler,
        CasesSaleService $casesSaleService,
        $config = []
    )
    {
        parent::__construct($id, $module, $logger, $config);
        $this->createHandler = $createHandler;
        $this->casesSaleService = $casesSaleService;
    }

    /**
     * @api {post} /v2/cases/create Create Case
     * @apiVersion 0.2.0
     * @apiName CreateCase
     * @apiGroup Cases
     * @apiPermission Authorized User
     *
     * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
     * @apiHeaderExample {json} Header-Example:
     *  {
     *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
     *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
     *  }
     *
     * @apiParam {string{160}}           contact_email                    Client Email
     * @apiParam {string{20}}           contact_phone                    Client Phone
     * @apiParam {int}              category_id                      Case category id
     * @apiParam {string{5..7}}     order_uid                        Order uid (symbols and numbers only)
     * @apiParam {string{255}}           [subject]                        Subject
     * @apiParam {string{65000}}           [description                     Description
     * @apiParam {array[]}          [order_info]                      Order Info (key => value, key: string, value: string)
     *
     * @apiParamExample {json} Request-Example:
     * {
     *       "contact_email": "test@test.com",
     *       "contact_phone": "+37369636690",
     *       "category_id": 12,
     *       "order_uid": "12WS09W",
     *       "subject": "Subject text",
     *       "description": "Description text",
     *       "order_info": {
     *           "Departure Date":"2020-03-07",
     *           "Departure Airport":"LON"
     *       }
     *   }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK
     *   {
     *       "status": 200,
     *       "message": "OK",
     *       "data": {
     *           "case_gid": "708ddf3e44ec477f8807d8b5f748bb6c",
     *           "client_uuid": "5d0cd25a-7f22-4b18-9547-e19a3e7d0c9a"
     *       },
     *       "technical": {
     *           "action": "v2/cases/create",
     *           "response_id": 11934216,
     *           "request_dt": "2020-03-17 08:31:30",
     *           "response_dt": "2020-03-17 08:31:30",
     *           "execution_time": 0.156,
     *           "memory_usage": 979248
     *       },
     *       "request": {
     *           "contact_email": "test@test.com",
     *           "contact_phone": "+37369636690",
     *           "category_id": 12,
     *           "order_uid": "12WS09W",
     *           "subject": "Subject text",
     *           "description": "Description text",
     *           "order_info": {
     *               "Departure Date": "2020-03-07",
     *               "Departure Airport": "LON"
     *           }
     *       }
     *   }
     *
     * @apiErrorExample {json} Error-Response(Validation error) (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     *   {
     *       "status": 422,
     *       "message": "Validation error",
     *       "errors": {
     *           "contact_email": [
     *               "Contact Email cannot be blank."
     *           ],
     *           "contact_phone": [
     *               "The format of Contact Phone is invalid."
     *           ],
     *           "order_uid": [
     *               "Order Uid should contain at most 7 characters."
     *           ]
     *       },
     *       "code": "21301",
     *       "technical": {
     *          ...
     *       },
     *       "request": {
     *          ...
     *       }
     *   }
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
     *       "code": 21101,
     *       "technical": {
     *           ...
     *       },
     *       "request": {
     *           ...
     *       }
     * }
     *
     * @apiErrorExample {json} Error-Response(Load data error) (400):
     *
     * HTTP/1.1 400 Bad Request
     * {
     *       "status": 400,
     *       "message": "Load data error",
     *       "errors": [
     *           "Not found Case data on POST request"
     *       ],
     *       "code": 21300,
     *       "technical": {
     *           ...
     *       },
     *       "request": {
     *           ...
     *       }
     * }
     */
    public function actionCreate(): Response
    {
        if (!$this->auth->au_project_id) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('Not found Project with current user: ' . $this->auth->au_api_username),
                new ErrorsMessage('Not found Project with current user: ' . $this->auth->au_api_username),
                new CodeMessage(ApiCodeException::NOT_FOUND_PROJECT_CURRENT_USER)
            );
        }

        $form = new CreateForm($this->auth->au_project_id);

        if (!$form->load(Yii::$app->request->post())) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found Case data on POST request'),
                new CodeMessage(CaseCodeException::API_CASE_CREATE_NOT_FOUND_DATA_ON_REQUEST)
            );
        }

        if (!$form->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($form->getErrors()),
                new CodeMessage(CaseCodeException::API_CASE_CREATE_VALIDATE)
            );
        }

        try {
            $result = $this->createHandler->handle($form->getDto());
        } catch (\Throwable $e) {
            return new ErrorResponse(
                new MessageMessage($e->getMessage()),
                new ErrorsMessage($e->getMessage()),
                new CodeMessage($e->getCode())
            );
        }

        $this->createSale($form, $result);

        return new SuccessResponse(
            new DataMessage(
                new Message('case_gid', $result->caseGid),
                new Message('client_uuid', $result->clientUuid),
            )
        );
    }

    /**
     * @param CreateForm $form
     * @param $result
     */
    private function createSale(CreateForm $form, $result):void
    {
        try {
            if ($saleData = $this->getSaleFromBo($form)) {
                $cases = Cases::findOne($result->csId);
                $caseSale = $this->casesSaleService->create($cases, $saleData);
                $refreshSaleData = $this->casesSaleService->detailRequestToBackOffice($saleData['saleId']);
                $this->casesSaleService->saveAdditionalData($caseSale, $cases, $refreshSaleData);
            }
        } catch (\Throwable $throwable) {
            Yii::error(VarDumper::dumpAsString($throwable), 'CasesController:create:getAndCreateSale' );
        }
    }

    /**
     * @param CreateForm $form
     * @return array|mixed
     */
    private function getSaleFromBo(CreateForm $form)
    {
        if ($findByOrderUid = $this->casesSaleService->searchRequestToBackOffice(['order_uid' => $form->order_uid])) {
            return $findByOrderUid;
        }
        if ($findByEmail = $this->casesSaleService->searchRequestToBackOffice(['email' => $form->contact_email])) {
            return $findByEmail;
        }
        if ($findByPhone = $this->casesSaleService->searchRequestToBackOffice(['phone' => $form->contact_phone])) {
            return $findByPhone;
        }
        return [];
    }
}

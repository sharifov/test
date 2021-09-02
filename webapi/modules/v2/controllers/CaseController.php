<?php

namespace webapi\modules\v2\controllers;

use common\components\jobs\CreateSaleFromBOJob;
use common\components\jobs\SendEmailOnCaseCreationBOJob;
use common\models\CaseSale;
use common\models\Department;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesStatus;
use sales\entities\cases\CasesQuery;
use sales\helpers\app\AppHelper;
use sales\model\cases\CaseCodeException;
use sales\model\cases\useCases\cases\api\create\CreateForm;
use sales\model\cases\useCases\cases\api\create\Handler;
use sales\services\cases\CasesSaleService;
use webapi\src\response\behaviors\RequestBehavior;
use webapi\src\response\behaviors\ResponseStatusCodeBehavior;
use webapi\behaviors\HttpBasicAuthHealthCheck;
use webapi\src\ApiCodeException;
use webapi\src\forms\cases\GetCasesByPhoneForm;
use webapi\src\forms\cases\GetCasesByEmailForm;
use webapi\src\forms\cases\GetCaseByCaseGidForm;
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
use yii\base\Model;
use yii\db\Expression;
use yii\helpers\VarDumper;

/**
 * Class CaseController
 *
 * @property Handler $createHandler
 * @property CasesSaleService $casesSaleService
 */
class CaseController extends BaseController
{
//    private $createHandler;
//    private $caseService;

//    public function __construct(
//        $id,
//        $module,
//        ApiLogger $logger,
//        Handler $createHandler,
//        $config = []
//    ) {
//        parent::__construct($id, $module, $logger, $config);
//        $this->createHandler = $createHandler;
//    }

    protected function verbs(): array
    {
        $verbs = parent::verbs();
        $verbs['get-list-by-phone'] = ['GET'];
        $verbs['find-list-by-phone'] = ['GET'];
        $verbs['get-list-by-email'] = ['GET'];
        $verbs['find-list-by-email'] = ['GET'];
        $verbs['get'] = ['GET'];
        return $verbs;
    }

    /**
     * @api {get} /v2/case/actionGetListByPhone Get Case
     * @apiVersion 0.2.0
     * @apiName getCases
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
     * @apiParam {string{160}}          contact_email                    Client Email required if contact phone or chat_visitor_id or order_uid are not set
     * @apiParam {string{20}}           contact_phone                    Client Phone required if contact email or chat_visitor_id or order_uid are not set
     * @apiParam {string{20}}           [contact_name]                   Client Name
     * @apiParam {string{50}}           chat_visitor_id                  Client chat_visitor_id required if contact phone or email or order_uid are not set
     * @apiParam {int}                  category_id                      Case category id
     * @apiParam {string{5..7}}         order_uid                        Order uid (symbols and numbers only) required if contact phone or email or chat_visitor_id are not set
     * @apiParam {string{100}}          [project_key]                    Project Key (if not exist project assign API User)
     * @apiParam {string{255}}          [subject]                        Subject
     * @apiParam {string{65000}}        [description]                    Description
     * @apiParam {array[]}              [order_info]                     Order Info (key => value, key: string, value: string)
     *
     * @apiParamExample {json} Request-Example:
     * {
     *       "contact_email": "test@test.com",
     *       "contact_phone": "+37369636690",
     *       "category_id": 12,
     *       "order_uid": "12WS09W",
     *       "subject": "Subject text",
     *       "description": "Description text",
     *       "project_key": "project_key",
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
     *           "case_id": 2354356,
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
     *           "project_key": "project_key",
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
    public function actionGetListByPhone(): Response
    {
        $form = new GetCasesByPhoneForm();
        if (!$form->load(Yii::$app->request->get())) {
            return $this->getCasesLoadDataErrorResponse();
        }
        if (!$form->validate()) {
            return $this->getCasesValidationErrorResponse($form);
        }

        return $this->getCasesResult(CasesQuery::getCasesByPhone($form->contact_phone, $form->active_only, $form->results_limit, $form->case_project_id, $form->case_department_id));
    }

    public function actionGetListByEmail(): Response
    {
        $form = new GetCasesByEmailForm();
        if (!$form->load(Yii::$app->request->get())) {
            return $this->getCasesLoadDataErrorResponse();
        }
        if (!$form->validate()) {
            return $this->getCasesValidationErrorResponse($form);
        }

        return $this->getCasesResult(CasesQuery::getCasesByEmail($form->contact_email, $form->active_only, $form->results_limit, $form->case_project_id, $form->case_department_id));
    }

    public function actionGet(): Response
    {
        $form = new GetCaseByCaseGidForm();
        if (!$form->load(Yii::$app->request->get())) {
            return $this->getCasesLoadDataErrorResponse();
        }
        if (!$form->validate()) {
            return $this->getCasesValidationErrorResponse($form);
        }

        return $this->getCasesResult(CasesQuery::getCaseByCaseGid($form->case_gid));
    }

    public function actionFindListByPhone(): Response
    {
        $form = new GetCasesByPhoneForm();
        if (!$form->load(Yii::$app->request->get())) {
            return $this->getCasesLoadDataErrorResponse();
        }
        if (!$form->validate()) {
            return $this->getCasesValidationErrorResponse($form);
        }

        return $this->getCasesResult(CasesQuery::findCasesGidByPhone($form->contact_phone, $form->active_only, $form->results_limit, $form->case_project_id, $form->case_department_id));
    }

    public function actionFindListByEmail(): Response
    {
        $form = new GetCasesByEmailForm();
        if (!$form->load(Yii::$app->request->get())) {
            return $this->getCasesLoadDataErrorResponse();
        }
        if (!$form->validate()) {
            return $this->getCasesValidationErrorResponse($form);
        }

        return $this->getCasesResult(CasesQuery::findCasesGidByEmail($form->contact_email, $form->active_only, $form->results_limit, $form->case_project_id, $form->case_department_id));
    }

    private function getCasesLoadDataErrorResponse(): ErrorResponse
    {
        return new ErrorResponse(
            new StatusCodeMessage(400),
            new MessageMessage(Messages::LOAD_DATA_ERROR),
            new ErrorsMessage('Not found GET request params'),
            new CodeMessage(CaseCodeException::API_GET_CASES_NOT_FOUND_DATA_ON_REQUEST)
        );
    }

    private function getCasesValidationErrorResponse(Model $form): ErrorResponse
    {
        return new ErrorResponse(
            new MessageMessage(Messages::VALIDATION_ERROR),
            new ErrorsMessage($form->getErrors()),
            new CodeMessage(CaseCodeException::API_GET_CASES_VALIDATE)
        );
    }

    private function getCasesResult(array $cases)
    {
        try {
            return new SuccessResponse(
                new DataMessage(
                    new Message('cases_array', $cases),
                )
            );
        } catch (\Throwable $e) {
            return new ErrorResponse(
                new MessageMessage($e->getMessage()),
                new ErrorsMessage($e->getMessage()),
                new CodeMessage($e->getCode())
            );
        }
    }
}

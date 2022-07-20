<?php

namespace webapi\modules\v2\controllers;

use common\components\jobs\CreateSaleFromBOJob;
use common\components\jobs\SendEmailOnCaseCreationBOJob;
use common\models\Lead;
use modules\experiment\models\ExperimentTarget;
use src\entities\cases\CaseCategory;
use src\entities\cases\Cases;
use src\helpers\app\AppHelper;
use src\model\cases\CaseCodeException;
use src\model\cases\useCases\cases\api\create\CreateForm;
use src\model\cases\useCases\cases\api\create\Handler;
use src\repositories\NotFoundException;
use src\services\cases\CasesSaleService;
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
    ) {
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
     * @apiParam {string{160}}          contact_email                    Client Email required if contact phone or chat_visitor_id or order_uid are not set
     * @apiParam {string{20}}           contact_phone                    Client Phone required if contact email or chat_visitor_id or order_uid are not set
     * @apiParam {string{20}}           [contact_name]                   Client Name
     * @apiParam {string{50}}           chat_visitor_id                  Client chat_visitor_id required if contact phone or email or order_uid are not set
     * @apiParam {int}                  [category_id]                    Case category id (Required if "category_key" is empty)
     * @apiParam {string{50}}           [category_key]                   Case category key (Required if "category_id" is empty - takes precedence over "category_id". See list in api "/v2/case-category/list")
     * @apiParam {string{5..7}}         order_uid                        Order uid (symbols and numbers only) required if contact phone or email or chat_visitor_id are not set
     * @apiParam {string{100}}          [project_key]                    Project Key (if not exist project assign API User)
     * @apiParam {string{255}}          [subject]                        Subject
     * @apiParam {string{65000}}        [description]                    Description
     * @apiParam {array[]}              [order_info]                     Order Info (key => value, key: string, value: string)
     * @apiParam {object[]}             [experiments]                    Cross system Experiments (array of objects {key=>value}, key = "ex_code", value = Experiment code (string))
     *
     * @apiParamExample {json} Request-Example:
     * {
     *       "contact_email": "test@test.com",
     *       "contact_phone": "+37369636690",
     *       "category_key": "voluntary_exchange",
     *       "category_id": null,
     *       "order_uid": "12WS09W",
     *       "subject": "Subject text",
     *       "description": "Description text",
     *       "project_key": "project_key",
     *       "order_info": {
     *           "Departure Date":"2020-03-07",
     *           "Departure Airport":"LON"
     *       },
     *       "experiments": [
     *           {
     *              "ex_code": "wpl5.0"
     *           },
     *           {
     *              "ex_code": "wpl6.2"
     *           }
     *       ]
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
    public function actionCreate(): Response
    {

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

        if (!$this->auth->au_project_id && !$form->project_key) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage('Not found project Key or Project for API user ' . $this->auth->au_api_username),
                new ErrorsMessage('Not found project Key or Project for API user ' . $this->auth->au_api_username),
                new CodeMessage(ApiCodeException::NOT_FOUND_PROJECT_CURRENT_USER)
            );
        }

        $caseCategory = $form->getCaseCategory();

        if (
            $form->order_uid && $case = Cases::find()
            ->andWhere(['cs_category_id' => $caseCategory->cc_id, 'cs_order_uid' => $form->order_uid])
            ->withNotFinishStatus()->limit(1)->one()
        ) {
            ExperimentTarget::processExperimentObjects(ExperimentTarget::EXT_TYPE_CASE, $case->cs_id, $form->experiments);

            return new SuccessResponse(
                new DataMessage(
                    new Message('case_id', $case->cs_id),
                    new Message('case_gid', $case->cs_gid),
                    new Message('client_uuid', $case->client ? $case->client->uuid : ''),
                )
            );
        }

        try {
            $result = $this->createHandler->handle($form->getDto(), $caseCategory);
        } catch (\Throwable $e) {
            return new ErrorResponse(
                new MessageMessage($e->getMessage()),
                new ErrorsMessage($e->getMessage()),
                new CodeMessage($e->getCode())
            );
        }

        ExperimentTarget::processExperimentObjects(ExperimentTarget::EXT_TYPE_CASE, $result->csId, $form->experiments);

        if ($form->order_uid || $form->contact_email || $form->contact_phone) {
            try {
                $job = new CreateSaleFromBOJob();
                $job->case_id = $result->csId;
                $job->order_uid = $form->order_uid;
                $job->email = $form->contact_email;
                $job->phone = $form->contact_phone;
                $job->project_key = $form->project_key;
                Yii::$app->queue_job->priority(100)->push($job);
            } catch (\Throwable $throwable) {
                Yii::error(
                    AppHelper::throwableLog($throwable),
                    'API:CasesController:actionCreate:CreateSaleFromBOJob'
                );
            }
        }

        if ($form->contact_email) {
            try {
                $job = new SendEmailOnCaseCreationBOJob();
                $job->case_id = $result->csId;
                $job->contact_email = $form->contact_email;
                Yii::$app->queue_email_job->priority(10)->push($job);
            } catch (\Throwable $throwable) {
                Yii::error(
                    AppHelper::throwableLog($throwable),
                    'API:CasesController:actionCreate:SendEmailOnCaseCreationBOJob'
                );
            }
        }

        return new SuccessResponse(
            new DataMessage(
                new Message('case_id', $result->csId),
                new Message('case_gid', $result->caseGid),
                new Message('client_uuid', $result->clientUuid),
            )
        );
    }
}

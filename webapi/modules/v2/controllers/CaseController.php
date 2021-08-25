<?php

namespace webapi\modules\v2\controllers;

use common\components\jobs\CreateSaleFromBOJob;
use common\components\jobs\SendEmailOnCaseCreationBOJob;
use common\models\Department;
use sales\entities\cases\Cases;
use sales\helpers\app\AppHelper;
use sales\model\cases\CaseCodeException;
use sales\model\cases\useCases\cases\api\create\CreateForm;
use sales\model\cases\useCases\cases\api\create\Handler;
use sales\services\cases\CasesSaleService;
use webapi\src\response\behaviors\RequestBehavior;
use webapi\src\response\behaviors\ResponseStatusCodeBehavior;
use webapi\behaviors\HttpBasicAuthHealthCheck;
use webapi\src\ApiCodeException;
use webapi\src\forms\cases\CaseRequestApiForm;
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
 * Class CaseController
 *
 * @property Handler $createHandler
 * @property CasesSaleService $casesSaleService
 */
class CaseController extends BaseController
{
    private $createHandler;
    private $caseService;

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
        $this->caseService = $casesSaleService;
    }

//    public function behaviors()
//    {
//        $behaviors = parent::behaviors();
//        $behaviors['contentNegotiator'] = [
//            'class' => 'yii\filters\ContentNegotiator',
//            'formats' => [
//                'application/plain' => \yii\web\Response::FORMAT_JSON,
//            ]
//        ];
//        return $behaviors;
//    }

    protected function verbs(): array
    {
        $verbs = parent::verbs();
        $verbs['find-list-by-phone'] = ['GET'];
        return $verbs;
    }

    /**
     * @api {get} /v2/case/actionFindListByPhone Get Case
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
    public function actionFindListByPhone(): Response
    {

        $form = new CaseRequestApiForm();

        if (!$form->load(Yii::$app->request->get())) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found Phone data on GET request'),
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

        $deps_params = [];
        if ($form->active_only == 'true') {
            $deps = Department::find()->all();
            foreach ($deps as $dep) {
                $deps_params[$dep->dep_id] = $dep->getParams()->object->case->trashActiveDaysLimit;
            }

            $cases = Cases::find()->findActiveCases(
                $form->contact_phone,
                $deps_params,
                Yii::$app->params['settings']['trash_cases_active_days_limit'],
                $form->limit,
                $form->case_project_id,
                $form->case_department_id,
            )->all();

            return new SuccessResponse(
                new DataMessage(
                    new Message('cases_array', $cases), # array_column($cases, 'cs_gid')),
                )
            );
        } else {
            $where = ['client_phone.phone' => $form->contact_phone];
            if ($form->case_project_id) {
                $where['cs_project_id'] = $form->case_project_id;
            }
            if ($form->case_department_id) {
                $where['cs_dep_id'] = $form->case_department_id;
            }

            if (
                $cases = Cases::find()
                ->select('cs_gid')
                ->leftJoin('client_phone', 'cs_client_id = client_phone.client_id')
                ->andWhere($where)
                ->orderBy('cs_created_dt ASC')
                ->limit($form->limit)->all()
//                ->createCommand()->getRawSql()
            ) {
                return new SuccessResponse(
                    new DataMessage(
                        new Message('cases_array', array_column($cases, 'cs_gid')),
                        new Message('temp', $cases),
                    )
                );
            }
        }

//        try {
//            $result =
//        } catch (\Throwable $e) {
//            return new ErrorResponse(
//                new MessageMessage($e->getMessage()),
//                new ErrorsMessage($e->getMessage()),
//                new CodeMessage($e->getCode())
//            );
//        }

        return new SuccessResponse(
            new DataMessage(
                new Message('case_ids', 33),
            )
        );
    }
}

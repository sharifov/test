<?php

namespace webapi\modules\v2\controllers;

use common\components\jobs\CreateSaleFromBOJob;
use common\components\jobs\SendEmailOnCaseCreationBOJob;
use common\models\CaseSale;
use common\models\Department;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesStatus;
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
use webapi\src\forms\cases\CaseApiResponse;
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
    private $createHandler;
    private $caseService;

    public function __construct(
        $id,
        $module,
        ApiLogger $logger,
        Handler $createHandler,
        $config = []
    ) {
        parent::__construct($id, $module, $logger, $config);
        $this->createHandler = $createHandler;
    }

    protected function verbs(): array
    {
        $verbs = parent::verbs();
        $verbs['get-list-by-phone'] = ['GET'];
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

        $where = ['client_phone.phone' => $form->contact_phone];
        if ($form->case_project_id) {
            $where['cs_project_id'] = $form->case_project_id;
        }
        if ($form->case_department_id) {
            $where['cs_dep_id'] = $form->case_department_id;
        }

        $query = CaseApiResponse::find()
            ->select('cs_id, cs_gid, cs_status, cs_created_dt, cs_updated_dt, cs_last_action_dt, cs_category_id, cs_project_id, cs_order_uid, projects.name')
            ->leftJoin('client_phone', 'cs_client_id = client_phone.client_id')
            ->leftJoin('projects', 'cs_project_id = projects.id')
            ->addSelect(new Expression('
                        DATE(if(last_out_date IS NULL, last_in_date, IF(last_in_date is NULL, last_out_date, LEAST(last_in_date, last_out_date)))) AS nextFlight'))
            ->leftJoin([
                'sale_out' => CaseSale::find()
                    ->select([
                        'css_cs_id',
                        new Expression('
                        MIN(css_out_date) AS last_out_date'),
                    ])
                    ->innerJoin(
                        Cases::tableName() . ' AS cases',
                        'case_sale.css_cs_id = cases.cs_id AND cases.cs_status = ' . CasesStatus::STATUS_PENDING
                    )
                    ->where('css_out_date >= SUBDATE(CURDATE(), 1)')
                    ->groupBy('css_cs_id')
            ], 'cases.cs_id = sale_out.css_cs_id')
            ->leftJoin([
                'sale_in' => CaseSale::find()
                    ->select([
                        'css_cs_id',
                        new Expression('
                        MIN(css_in_date) AS last_in_date'),
                    ])
                    ->innerJoin(
                        Cases::tableName() . ' AS cases',
                        'case_sale.css_cs_id = cases.cs_id AND cases.cs_status = ' . CasesStatus::STATUS_PENDING
                    )
                    ->where('css_in_date >= SUBDATE(CURDATE(), 1)')
                    ->groupBy('css_cs_id')
            ], 'cases.cs_id = sale_in.css_cs_id')
            ->andWhere($where)
            ->orderBy('cs_created_dt ASC')
            ->limit($form->limit);

        if ($form->active_only != true) {
            $cases = $query->all();
            //                ->createCommand()->getRawSql()

            return new SuccessResponse(
                new DataMessage(
                    new Message('cases_array', $cases),  // array_column($cases, 'cs_gid')
                )
            );
        } else {
            $deps_params = [];
            $deps = Department::find()->all();
            foreach ($deps as $dep) {
                $deps_params[$dep->dep_id] = $dep->getParams()->object->case->trashActiveDaysLimit;
            }

            $query
//                ->addSelect(new Expression(' IF ( cs_created_dt < NOW() - INTERVAL ' . $limit . ' DAY, true, false) AS is_active,'))
                ->andWhere('cs_status != ' . CasesStatus::STATUS_SOLVED);

            $cases = $query->all();

            $trashCasesActiveDaysLimitGlobal = Yii::$app->params['settings']['trash_cases_active_days_limit'] ?? 0;
            $result = [];
            if (is_countable($cases)) {
                foreach ($cases as $case) {
                    $case->status_name = CasesStatus::getName($case->cs_status);
                    $days_limit = $deps_params[$case->cs_dep_id] ?? $trashCasesActiveDaysLimitGlobal;
                    $limit_dt = (new \DateTimeImmutable($case->cs_created_dt))->modify('+' . $days_limit . 'day');
                    if (
                        $case->cs_status == CasesStatus::STATUS_TRASH && (strtotime($limit_dt->format('Y-m-d H:i:s')) > time())
                    ) {
                        $result[] = $case;
                    }
                }
            }

            return new SuccessResponse(
                new DataMessage(
                    new Message('cases_array', $result),
                )
            );
        }
    //        } else {
    //        }

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

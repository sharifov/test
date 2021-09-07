<?php

namespace webapi\modules\v2\controllers;

use sales\entities\cases\CasesQuery;
use sales\model\cases\CaseCodeException;
use webapi\src\forms\cases\GetCasesByPhoneForm;
use webapi\src\forms\cases\GetCasesByEmailForm;
use webapi\src\forms\cases\GetCaseByCaseGidForm;
use webapi\src\Messages;
use webapi\src\response\ErrorResponse;
use webapi\src\response\messages\CodeMessage;
use webapi\src\response\messages\DataMessage;
use webapi\src\response\messages\ErrorsMessage;
use webapi\src\response\messages\Message;
use webapi\src\response\messages\MessageMessage;
use webapi\src\response\messages\StatusCodeMessage;
use webapi\src\response\SuccessResponse;
use webapi\src\response\Response;
use Yii;
use yii\base\Model;

/**
 * Class CaseController
 */
class CaseController extends BaseController
{

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
     * @api {get} /v2/case/get-list-by-phone Get Cases list by Client Phone number
     * @apiVersion 0.2.0
     * @apiName getCasesListByPhone
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
     * @apiParam {string{20}}           contact_phone                    Client Phone required
     * @apiParam {bool}                 active_only                      "true" for requesting active cases only (depends on Department->object->case->trashActiveDaysLimit or global trash_cases_active_days_limit Site setting) or "false" for all cases
     * @apiParam {int}                  [cases_department_id]            Department ID
     * @apiParam {int}                  [cases_project_id]               Project ID
     * @apiParam {int}                  [results_limit]                  Limits number of cases in results list
     *
     * @apiParamExample {json} Request-Example:
     * {
     *       "contact_phone": "+18888888888",
     *       "active_only": "true",
     *       "cases_department_id": 2,
     *       "cases_project_id": 6,
     *       "results_limit": 10
     *   }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK

     * {
     *     "status": 200,
     *     "message": "OK",
     *     "data": [
     *             {
     *                 "case_id": "88473",
     *                 "case_gid": "c5f3f405ea489bd6e6a1f3886086c9d9",
     *                 "case_created_dt": "2020-02-26 15:26:25",
     *                 "case_updated_dt": "2020-02-26 17:07:18",
     *                 "case_last_action_dt": "2020-02-27 15:08:39",
     *                 "case_category_id": "16",
     *                 "case_order_uid": "P6QWNH",
     *                 "case_project_name": "ARANGRANT",
     *                 "case_next_flight": "2022-05-22",
     *                 "case_status_name": "Processing"
     *             },
     *             {
     *                 "case_id": "130705",
     *                 "case_gid": "37129b222479f0468d6355fcf4bd0235",
     *                 "case_created_dt": "2020-03-24 09:14:28",
     *                 "case_updated_dt": "2020-03-24 11:00:34",
     *                 "case_last_action_dt": "2020-03-24 11:00:34",
     *                 "case_category_id": "16",
     *                 "case_order_uid": null,
     *                 "case_project_name": "WOWFARE",
     *                 "case_next_flight": null,
     *                 "case_status_name": "Processing"
     *             }
     *     ],
     *     "technical": {
     *         "action": "v2/case/get-list-by-phone",
     *         "response_id": 753,
     *         "request_dt": "2021-09-02 13:52:53",
     *         "response_dt": "2021-09-02 13:52:53",
     *         "execution_time": 0.029,
     *         "memory_usage": 568056
     *     },
     *     "request": []
     * }
     *
     * @apiErrorExample {json} Error-Response(Validation error) (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     * {
     *     "status": 422,
     *     "message": "Validation error",
     *     "errors": {
     *         "contact_phone": [
     *             "The format of Contact Phone is invalid."
     *         ]
     *     },
     *     "code": "21303",
     *     "technical": {
     *         "action": "v2/case/get-list-by-phone",
     *         "response_id": 754,
     *         "request_dt": "2021-09-02 14:01:22",
     *         "response_dt": "2021-09-02 14:01:22",
     *         "execution_time": 0.028,
     *         "memory_usage": 306800
     *     },
     *     "request": []
     * }
     *
     * @apiErrorExample {json} Error-Response (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     * {
     *       "status": 422,
     *       "message": "Validation error",
     *       "errors": {
     *           "contact_phone": [
     *               "Client Phone number not found in DB."
     *           ]
     *       },
     *       "code": 21303,
     *       "technical": {
     *           ...
     *       },
     *       "request": []
     * }
     *
     * @apiErrorExample {json} Error-Response(Load data error) (400):
     *
     * HTTP/1.1 400 Bad Request
     * {
     *       "status": 400,
     *       "message": "Load data error",
     *       "errors": [
     *           "Not found  GET request params"
     *       ],
     *       "code": 21302,
     *       "technical": {
     *           ...
     *       },
     *       "request":  []
     * }
     *
     * @return \webapi\src\response\Response
     * @throws \Throwable
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

        return $this->getCasesResult(CasesQuery::findCasesByPhone($form->contact_phone, $form->active_only, $form->results_limit, $form->cases_project_id, $form->cases_department_id));
    }


    /**
     * @api {get} /v2/case/get-list-by-email Get Cases list by Client Email
     * @apiVersion 0.2.0
     * @apiName getCasesListByEmail
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
     * @apiParam {string{320}}           contact_email                    Client Email required
     * @apiParam {bool}                 active_only                      "true" for requesting active cases only (depends on Department->object->case->trashActiveDaysLimit or global trash_cases_active_days_limit Site setting) or "false" for all cases
     * @apiParam {int}                  [cases_department_id]            Department ID
     * @apiParam {int}                  [cases_project_id]               Project ID
     * @apiParam {int}                  [results_limit]                  Limits number of cases in results list
     *
     * @apiParamExample {json} Request-Example:
     * {
     *       "contact_email": "test@test.test",
     *       "active_only": "true",
     *       "cases_department_id": 2,
     *       "cases_project_id": 6,
     *       "results_limit": 10
     *   }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK

     * {
     *     "status": 200,
     *     "message": "OK",
     *     "data": [
     *             {
     *                 "case_id": "88473",
     *                 "case_gid": "c5f3f405ea489bd6e6a1f3886086c9d9",
     *                 "case_created_dt": "2020-02-26 15:26:25",
     *                 "case_updated_dt": "2020-02-26 17:07:18",
     *                 "case_last_action_dt": "2020-02-27 15:08:39",
     *                 "case_category_id": "16",
     *                 "case_order_uid": "P6QWNH",
     *                 "case_project_name": "ARANGRANT",
     *                 "case_next_flight": "2022-05-22",
     *                 "case_status_name": "Processing"
     *             },
     *             {
     *                 "case_id": "130705",
     *                 "case_gid": "37129b222479f0468d6355fcf4bd0235",
     *                 "case_created_dt": "2020-03-24 09:14:28",
     *                 "case_updated_dt": "2020-03-24 11:00:34",
     *                 "case_last_action_dt": "2020-03-24 11:00:34",
     *                 "case_category_id": "16",
     *                 "case_order_uid": null,
     *                 "case_project_name": "WOWFARE",
     *                 "case_next_flight": null,
     *                 "case_status_name": "Processing"
     *             }
     *     ],
     *     "technical": {
     *         "action": "v2/case/get-list-by-email",
     *         "response_id": 753,
     *         "request_dt": "2021-09-02 13:52:53",
     *         "response_dt": "2021-09-02 13:52:53",
     *         "execution_time": 0.029,
     *         "memory_usage": 568056
     *     },
     *     "request": []
     * }
     *
     * @apiErrorExample {json} Error-Response(Validation error) (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     * {
     *     "status": 422,
     *     "message": "Validation error",
     *     "errors": {
     *         "contact_email": [
     *             "Contact Email is not a valid email address."
     *         ]
     *     },
     *     "code": "21303",
     *     "technical": {
     *         "action": "v2/case/get-list-by-email",
     *         "response_id": 754,
     *         "request_dt": "2021-09-02 14:01:22",
     *         "response_dt": "2021-09-02 14:01:22",
     *         "execution_time": 0.028,
     *         "memory_usage": 306800
     *     },
     *     "request": []
     * }
     *
     * @apiErrorExample {json} Error-Response (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     * {
     *       "status": 422,
     *       "message": "Validation error",
     *       "errors": {
     *           "contact_email": [
     *               "Client Email not found in DB."
     *           ]
     *       },
     *       "code": 21303,
     *       "technical": {
     *           ...
     *       },
     *       "request": []
     * }
     *
     * @apiErrorExample {json} Error-Response(Load data error) (400):
     *
     * HTTP/1.1 400 Bad Request
     * {
     *       "status": 400,
     *       "message": "Load data error",
     *       "errors": [
     *           "Not found  GET request params"
     *       ],
     *       "code": 21302,
     *       "technical": {
     *           ...
     *       },
     *       "request":  []
     * }
     *
     * @return \webapi\src\response\Response
     * @throws \Throwable
     */
    public function actionGetListByEmail(): Response
    {
        $form = new GetCasesByEmailForm();
        if (!$form->load(Yii::$app->request->get())) {
            return $this->getCasesLoadDataErrorResponse();
        }
        if (!$form->validate()) {
            return $this->getCasesValidationErrorResponse($form);
        }

        return $this->getCasesResult(CasesQuery::findCasesByEmail($form->contact_email, $form->active_only, $form->results_limit, $form->cases_project_id, $form->cases_department_id));
    }

    /**
     * @api {get} /v2/case/get Get data Case by Case GID
     * @apiVersion 0.2.0
     * @apiName getCaseDataByCaseGid
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
     * @apiParam {string{50}}           case_gid                         Client Email required
     *
     * @apiParamExample {json} Request-Example:
     * {
     *       "case_gid": "c5f3f405ea489bd6e6a1f3886086c9d9",
     * }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK

     * {
     *     "status": 200,
     *     "message": "OK",
     *     "data": {
     *                 "case_id": "88473",
     *                 "case_gid": "c5f3f405ea489bd6e6a1f3886086c9d9",
     *                 "case_created_dt": "2020-02-26 15:26:25",
     *                 "case_updated_dt": "2020-02-26 17:07:18",
     *                 "case_last_action_dt": "2020-02-27 15:08:39",
     *                 "case_category_id": "16",
     *                 "case_order_uid": "P6QWNH",
     *                 "case_project_name": "ARANGRANT",
     *                 "case_next_flight": "2022-05-22",
     *                 "case_status_name": "Processing"
     *     },
     *     "technical": {
     *         "action": "v2/case/get",
     *         "response_id": 753,
     *         "request_dt": "2021-09-02 13:52:53",
     *         "response_dt": "2021-09-02 13:52:53",
     *         "execution_time": 0.029,
     *         "memory_usage": 568056
     *     },
     *     "request": []
     * }
     *
     * @apiErrorExample {json} Error-Response(Validation error) (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     * {
     *     "status": 422,
     *     "message": "Validation error",
     *     "errors": [
     *             "Case with this case_gid not found."
     *     ],
     *     "code": "21304",
     *     "technical": {
     *         "action": "v2/case/get",
     *         "response_id": 754,
     *         "request_dt": "2021-09-02 14:01:22",
     *         "response_dt": "2021-09-02 14:01:22",
     *         "execution_time": 0.028,
     *         "memory_usage": 306800
     *     },
     *     "request": []
     * }
     *
     * @apiErrorExample {json} Error-Response(Validation error) (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     * {
     *     "status": 422,
     *     "message": "Validation error",
     *     "errors": {
     *         "case_gid": [
     *             "Case Gid should contain at most 50 characters."
     *         ]
     *     },
     *     "code": "21303",
     *     "technical": {
     *         "action": "v2/case/get",
     *         "response_id": 754,
     *         "request_dt": "2021-09-02 14:01:22",
     *         "response_dt": "2021-09-02 14:01:22",
     *         "execution_time": 0.028,
     *         "memory_usage": 306800
     *     },
     *     "request": []
     * }
     *
     * @apiErrorExample {json} Error-Response(Load data error) (400):
     *
     * HTTP/1.1 400 Bad Request
     * {
     *       "status": 400,
     *       "message": "Load data error",
     *       "errors": [
     *           "Not found  GET request params"
     *       ],
     *       "code": 21302,
     *       "technical": {
     *           ...
     *       },
     *       "request":  []
     * }
     *
     * @return \webapi\src\response\Response
     * @throws \Throwable
     */
    public function actionGet(): Response
    {
        $form = new GetCaseByCaseGidForm();
        if (!$form->load(Yii::$app->request->get())) {
            return $this->getCasesLoadDataErrorResponse();
        }
        if (!$form->validate()) {
            return $this->getCasesValidationErrorResponse($form);
        }

        $case = CasesQuery::findCaseByCaseGid($form->case_gid);

        try {
            if (empty($case)) {
                return new ErrorResponse(
                    new MessageMessage(Messages::VALIDATION_ERROR),
                    new ErrorsMessage("Case with this case_gid not found."),
                    new CodeMessage(CaseCodeException::API_GET_CASE_BY_GID_NOT_FOUND)
                );
            } else {
                return new SuccessResponse(
                    new DataMessage(
                        $case
                    )
                );
            }
        } catch (\Throwable $e) {
            return new ErrorResponse(
                new MessageMessage($e->getMessage()),
                new ErrorsMessage($e->getMessage()),
                new CodeMessage($e->getCode())
            );
        }
    }

    /**
     * @api {get} /v2/case/find-list-by-phone Get Cases GID list by Client Phone number
     * @apiVersion 0.2.0
     * @apiName findCasesListByPhone
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
     * @apiParam {string{20}}           contact_phone                    Client Phone required
     * @apiParam {bool}                 active_only                      "true" for requesting active cases only (depends on Department->object->case->trashActiveDaysLimit or global trash_cases_active_days_limit Site setting) or "false" for all cases
     * @apiParam {int}                  [cases_department_id]            Department ID
     * @apiParam {int}                  [cases_project_id]               Project ID
     * @apiParam {int}                  [results_limit]                  Limits number of cases in results list
     *
     * @apiParamExample {json} Request-Example:
     * {
     *       "contact_phone": "+18888888888",
     *       "active_only": "true",
     *       "cases_department_id": 2,
     *       "cases_project_id": 6,
     *       "results_limit": 10
     *   }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK

     * {
     *     "status": 200,
     *     "message": "OK",
     *     "data": [
     *             "24f12d06267aaa8e8ff86c5059efdf86",
     *             "20e1c76c70f86063ded79b6d389f490d",
     *             "c5f3f405ea489bd6e6a1f3886086c9d9",
     *     ],
     *     "technical": {
     *         "action": "v2/case/find-list-by-phone",
     *         "response_id": 753,
     *         "request_dt": "2021-09-02 13:52:53",
     *         "response_dt": "2021-09-02 13:52:53",
     *         "execution_time": 0.029,
     *         "memory_usage": 568056
     *     },
     *     "request": []
     * }
     *
     * @apiErrorExample {json} Error-Response(Validation error) (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     * {
     *     "status": 422,
     *     "message": "Validation error",
     *     "errors": {
     *         "contact_phone": [
     *             "The format of Contact Phone is invalid."
     *         ]
     *     },
     *     "code": "21303",
     *     "technical": {
     *         "action": "v2/case/find-list-by-phone",
     *         "response_id": 754,
     *         "request_dt": "2021-09-02 14:01:22",
     *         "response_dt": "2021-09-02 14:01:22",
     *         "execution_time": 0.028,
     *         "memory_usage": 306800
     *     },
     *     "request": []
     * }
     *
     * @apiErrorExample {json} Error-Response (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     * {
     *       "status": 422,
     *       "message": "Validation error",
     *       "errors": {
     *           "contact_phone": [
     *               "Client Phone number not found in DB."
     *           ]
     *       },
     *       "code": 21303,
     *       "technical": {
     *           ...
     *       },
     *       "request": []
     * }
     *
     * @apiErrorExample {json} Error-Response(Load data error) (400):
     *
     * HTTP/1.1 400 Bad Request
     * {
     *       "status": 400,
     *       "message": "Load data error",
     *       "errors": [
     *           "Not found  GET request params"
     *       ],
     *       "code": 21302,
     *       "technical": {
     *           ...
     *       },
     *       "request":  []
     * }
     *
     * @return \webapi\src\response\Response
     * @throws \Throwable
     */
    public function actionFindListByPhone(): Response
    {
        $form = new GetCasesByPhoneForm();
        if (!$form->load(Yii::$app->request->get())) {
            return $this->getCasesLoadDataErrorResponse();
        }
        if (!$form->validate()) {
            return $this->getCasesValidationErrorResponse($form);
        }

        return $this->getCasesResult(CasesQuery::findCasesGidByPhone($form->contact_phone, $form->active_only, $form->results_limit, $form->cases_project_id, $form->cases_department_id));
    }


    /**
     * @api {get} /v2/case/find-list-by-email Get Cases GID list by Client Email
     * @apiVersion 0.2.0
     * @apiName findCasesListByEmail
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
     * @apiParam {string{320}}           contact_email                    Client Email required
     * @apiParam {bool}                 active_only                      "true" for requesting active cases only (depends on Department->object->case->trashActiveDaysLimit or global trash_cases_active_days_limit Site setting) or "false" for all cases
     * @apiParam {int}                  [cases_department_id]            Department ID
     * @apiParam {int}                  [cases_project_id]               Project ID
     * @apiParam {int}                  [results_limit]                  Limits number of cases in results list
     *
     * @apiParamExample {json} Request-Example:
     * {
     *       "contact_email": "test@test.test",
     *       "active_only": "true",
     *       "cases_department_id": 2,
     *       "cases_project_id": 6,
     *       "results_limit": 10
     *   }
     *
     * @apiSuccessExample {json} Success-Response:
     *
     * HTTP/1.1 200 OK

     * {
     *     "status": 200,
     *     "message": "OK",
     *     "data": [
     *             "24f12d06267aaa8e8ff86c5059efdf86",
     *             "20e1c76c70f86063ded79b6d389f490d",
     *             "c5f3f405ea489bd6e6a1f3886086c9d9",
     *     ],
     *     "technical": {
     *         "action": "v2/case/find-list-by-email",
     *         "response_id": 753,
     *         "request_dt": "2021-09-02 13:52:53",
     *         "response_dt": "2021-09-02 13:52:53",
     *         "execution_time": 0.029,
     *         "memory_usage": 568056
     *     },
     *     "request": []
     * }
     *
     * @apiErrorExample {json} Error-Response(Validation error) (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     * {
     *     "status": 422,
     *     "message": "Validation error",
     *     "errors": {
     *         "contact_email": [
     *             "Contact Email is not a valid email address."
     *         ]
     *     },
     *     "code": "21303",
     *     "technical": {
     *         "action": "v2/case/find-list-by-email",
     *         "response_id": 754,
     *         "request_dt": "2021-09-02 14:01:22",
     *         "response_dt": "2021-09-02 14:01:22",
     *         "execution_time": 0.028,
     *         "memory_usage": 306800
     *     },
     *     "request": []
     * }
     *
     * @apiErrorExample {json} Error-Response (422):
     *
     * HTTP/1.1 422 Unprocessable entity
     * {
     *       "status": 422,
     *       "message": "Validation error",
     *       "errors": {
     *           "contact_email": [
     *               "Client Email not found in DB."
     *           ]
     *       },
     *       "code": 21303,
     *       "technical": {
     *           ...
     *       },
     *       "request": []
     * }
     *
     * @apiErrorExample {json} Error-Response(Load data error) (400):
     *
     * HTTP/1.1 400 Bad Request
     * {
     *       "status": 400,
     *       "message": "Load data error",
     *       "errors": [
     *           "Not found  GET request params"
     *       ],
     *       "code": 21302,
     *       "technical": {
     *           ...
     *       },
     *       "request":  []
     * }
     *
     * @return \webapi\src\response\Response
     * @throws \Throwable
     */
    public function actionFindListByEmail(): Response
    {
        $form = new GetCasesByEmailForm();
        if (!$form->load(Yii::$app->request->get())) {
            return $this->getCasesLoadDataErrorResponse();
        }
        if (!$form->validate()) {
            return $this->getCasesValidationErrorResponse($form);
        }

        return $this->getCasesResult(CasesQuery::findCasesGidByEmail($form->contact_email, $form->active_only, $form->results_limit, $form->cases_project_id, $form->cases_department_id));
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
                    $cases
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

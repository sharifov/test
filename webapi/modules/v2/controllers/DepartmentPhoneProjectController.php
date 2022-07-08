<?php

namespace webapi\modules\v2\controllers;

use common\models\Department;
use common\models\DepartmentPhoneProject;
use modules\experiment\models\ExperimentTarget;
use src\model\department\DepartmentCodeException;
use src\services\departmentPhoneProject\DepartmentPhoneProjectParamsService;
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
use src\model\department\departmentPhoneProject\useCases\api\get\DepartmentPhoneProjectForm;

/**
 * Class DepartmentPhoneProjectController
 */
class DepartmentPhoneProjectController extends BaseController
{
     /**
      * @api {post} /v2/department-phone-project/get Get Department Phone Project
      * @apiVersion 0.2.0
      * @apiName GetDepartmentPhoneProject
      * @apiGroup DepartmentPhoneProject
      * @apiPermission Authorized User
      *
      * @apiHeader {string} Authorization Credentials <code>base64_encode(Username:Password)</code>
      * @apiHeaderExample {json} Header-Example:
      *  {
      *      "Authorization": "Basic YXBpdXNlcjpiYjQ2NWFjZTZhZTY0OWQxZjg1NzA5MTFiOGU5YjViNB==",
      *      "Accept-Encoding": "Accept-Encoding: gzip, deflate"
      *  }
      *
      * @apiParam {int}                               project_id               Project ID
      * @apiParam {string=Sales, Exchange, Support}   [department]             Department
      *
      * @apiParamExample {json} Request-Example:
      *
      * {
      *     "project_id": 6,
      *     "department": "Sales"
      * }
      *
      * @apiSuccessExample {json} Success-Response:
      *
      * HTTP/1.1 200 OK
      * {
      *        "status": 200
      *        "message": "OK",
      *        "data": {
      *            "phones": [
      *                {
      *                    "phone": "+15211111111",
      *                    "cid": "WOWMAC",
      *                    "department_id": 1,
      *                    "department": "Sales",
      *                    "language_id": "en-US",
      *                    "updated_dt": "2019-01-08 11:44:57"
      *                    "experiments": [
      *                         "wpl5.0",
      *                         "wpl6.2"
      *                     ]
      *                },
      *                {
      *                    "phone": "+15222222222",
      *                    "cid": "WSUDCV",
      *                    "department_id": 3,
      *                    "department": "Support",
      *                    "language_id": "fr-FR",
      *                    "updated_dt": "2019-01-09 11:50:25"

      *               }
      *            ]
      *        },
      *        "technical": {
      *           ...
      *        },
      *        "request": {
      *           ...
      *        }
      * }
      *
      * @apiErrorExample {json} Error-Response (422):
      *
      * HTTP/1.1 422 Unprocessable entity
      * {
      *        "status": 422,
      *        "message": "Validation error",
      *        "errors": {
      *             "project_id": [
      *                 "Project Id cannot be blank."
      *             ],
      *             "department": [
      *                 "Department is invalid."
      *             ]
      *        },
      *        "code": "14301",
      *        "technical": {
      *           ...
      *        },
      *        "request": {
      *           ...
      *        }
      * }
      *
      * @apiErrorExample {json} Error-Response (400):
      *
      * HTTP/1.1 400 Bad Request
      * {
      *       "status": 400,
      *       "message": "Load data error",
      *       "errors": [
      *            "Not found Department Phone Project data on POST request"
      *       ],
      *       "code": "14300",
      *       "request": {
      *           ...
      *       },
      *       "technical": {
      *           ...
      *      }
      * }
      */
    public function actionGet()
    {
        $form = new DepartmentPhoneProjectForm();

        if (!$form->load(Yii::$app->request->post())) {
            return new ErrorResponse(
                new StatusCodeMessage(400),
                new MessageMessage(Messages::LOAD_DATA_ERROR),
                new ErrorsMessage('Not found Department Phone Project data on POST request'),
                new CodeMessage(DepartmentCodeException::API_DEPARTMENT_PHONE_PROJECT_GET_NOT_FOUND_DATA_ON_REQUEST)
            );
        }

        if (!$form->validate()) {
            return new ErrorResponse(
                new MessageMessage(Messages::VALIDATION_ERROR),
                new ErrorsMessage($form->getErrors()),
                new CodeMessage(DepartmentCodeException::API_DEPARTMENT_PHONE_PROJECT_GET_VALIDATE)
            );
        }

        /** @var DepartmentPhoneProject[] $phones */

        $phones = DepartmentPhoneProject::find()
            ->andWhere(['dpp_project_id' => $form->project_id])
            ->andWhere(['dpp_show_on_site' => true])
            ->andFilterWhere(['dpp_dep_id' => $form->department_id])
            ->withPhoneList()
            ->all();

        $data = [];

        foreach ($phones as $key => $phone) {
            $departmentPhone = DepartmentPhoneProject::find()->byPhone($phone->getPhone(), false)->enabled()->limit(1)->one();
            if ($departmentPhone) {
                $departmentPhoneProjectParamsService = new DepartmentPhoneProjectParamsService($departmentPhone);
            }

            $data[] = [
                'phone' => $phone->getPhone(),
                'cid' => $phone->dppSource ? $phone->dppSource->cid : null,
                'department_id' => $phone->dpp_dep_id,
                'experiments' => $departmentPhone ? $departmentPhoneProjectParamsService->getPhoneExperiments() : null,
                'department' => $phone->dpp_dep_id ? Department::getName($phone->dpp_dep_id) : null,
                'language_id' => $phone->dpp_language_id,
                'updated_dt' => $phone->dpp_updated_dt,
            ];
        }

        return new SuccessResponse(
            new DataMessage(
                new Message('phones', $data)
            )
        );
    }
}

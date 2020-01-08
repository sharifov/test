<?php

namespace webapi\modules\v2\controllers;

use common\models\DepartmentPhoneProject;
use sales\model\department\DepartmentCodeException;
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
use sales\model\department\departmentPhoneProject\useCases\api\get\DepartmentPhoneProjectForm;

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
      * @apiParam {int}            project_id                                   Project ID
      * @apiParam {int}            [source_id]                                  Source ID
      * @apiParam {int{1}=1-SALES, 2-EXCHANGE, 3-SUPPORT}   [department_id]     Department ID
      *
      * @apiParamExample {json} Request-Example:
      *
      * {
      *     "project_id": 6,
      *     "source_id": 44,
      *     "department_id": 1
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
      *                    "source_id": 40,
      *                    "department_id": 1
      *                },
      *                {
      *                    "phone": "+15222222222",
      *                    "source_id": 44,
      *                    "department_id": 2
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
      *             "source_id": [
      *                 "Source Id must be an integer."
      *             ],
      *             "department_id": [
      *                 "Department Id is invalid."
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

        $phones = DepartmentPhoneProject::find()
            ->andWhere(['dpp_project_id' => $form->project_id])
            ->andWhere(['dpp_show_on_site' => true])
            ->andFilterWhere(['dpp_source_id' => $form->source_id])
            ->andFilterWhere(['dpp_dep_id' => $form->department_id])
            ->all();

        $data = [];

        foreach ($phones as $key => $phone) {
            $data[] = [
                'phone' => $phone->dpp_phone_number,
                'source_id' => $phone->dpp_source_id,
                'department_id' => $phone->dpp_dep_id,
            ];
        }

        return new SuccessResponse(
            new DataMessage(
                new Message('phones', $data)
            )
        );
    }
}

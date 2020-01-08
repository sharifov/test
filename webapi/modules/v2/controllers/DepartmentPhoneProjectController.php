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

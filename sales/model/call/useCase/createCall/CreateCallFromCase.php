<?php

namespace sales\model\call\useCase\createCall;

use common\models\Call;
use sales\auth\Auth;
use sales\entities\cases\Cases;
use sales\model\call\services\FriendlyName;
use sales\model\call\services\RecordManager;
use sales\model\phone\AvailablePhoneList;

class CreateCallFromCase
{
    public function __invoke(CreateCallForm $form): array
    {
        try {
            if (!$case = Cases::findOne(['cs_id' => $form->caseId])) {
                throw new \DomainException('Not found Case. ID: ' . $form->caseId);
            }

            //todo: validate can created user call from this case?

            if (!$case->cs_project_id) {
                throw new \DomainException('Not found Project. Case ID: ' . $case->cs_id);
            }

            if (!$case->cs_dep_id) {
                throw new \DomainException('Not found Department. Case ID: ' . $case->cs_id);
            }

            $departmentParams = $case->department->getParams();
            if (!$departmentParams) {
                throw new \DomainException('Not found Department parameters. DepartmentId: ' . $case->cs_dep_id);
            }

            $availablePhones = new AvailablePhoneList($form->getCreatedUserId(), $case->cs_project_id, $case->cs_dep_id, $departmentParams->defaultPhoneType);
            if (!$availablePhones->isExist($form->from)) {
                throw new \DomainException('Phone From (' . $form->from . ') is not available.');
            }

            $recordDisabled = (RecordManager::createCall(
                Auth::id(),
                $case->cs_project_id,
                $case->cs_dep_id,
                $form->from,
                $case->cs_client_id
            ))->isDisabledRecord();

            $result = \Yii::$app->communication->createCall(
                new \sales\model\call\useCase\conference\create\CreateCallForm([
                    'user_identity' => $form->getClientDeviceIdentity(),
                    'user_id' => $form->getCreatedUserId(),
                    'to_number' => $form->to,
                    'from_number' => $form->from,
                    'phone_list_id' => $form->getPhoneListId(),
                    'project_id' => $case->cs_project_id,
                    'department_id' => $case->cs_dep_id,
                    'case_id' => $case->cs_id,
                    'client_id' => $case->cs_client_id,
                    'source_type_id' => Call::SOURCE_CASE,
                    'call_recording_disabled' => $recordDisabled,
                    'friendly_name' => FriendlyName::next(),
                ])
            );
        } catch (\Throwable $e) {
            $result = [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }

        return $result;
    }
}

<?php

namespace src\model\call\useCase\createCall\fromCase;

use common\models\Call;
use src\entities\cases\Cases;
use src\model\call\services\FriendlyName;
use src\model\call\services\RecordManager;

class CreateCallFromCase
{
    public function __invoke(\src\model\call\useCase\createCall\CreateCallForm $form): array
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

            $availablePhones = new AbacCallFromNumberList($form->createdUser, $case);
            if (!$availablePhones->isExist($form->from)) {
                throw new \DomainException('Phone From (' . $form->from . ') is not available.');
            }

            $recordDisabled = (RecordManager::createCall(
                $form->createdUser->id,
                $case->cs_project_id,
                $case->cs_dep_id,
                $form->from,
                $case->cs_client_id
            ))->isDisabledRecord();

            $result = \Yii::$app->communication->createCall(
                new \src\model\call\useCase\conference\create\CreateCallForm([
                    'device' => $form->getVoipDevice(),
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
                    'project' => $case->cs_project_id ? $case->project->name : '',
                    'source' => Call::SOURCE_LIST[Call::SOURCE_CASE],
                    'type' => Call::TYPE_LIST[Call::CALL_TYPE_OUT],
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

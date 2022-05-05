<?php

namespace src\model\call\useCase\createCall\fromLead;

use common\models\Call;
use common\models\Lead;
use src\model\call\services\FriendlyName;
use src\model\call\services\RecordManager;

class CreateCallFromLead
{
    public function __invoke(\src\model\call\useCase\createCall\CreateCallForm $form): array
    {
        try {
            if (!$lead = Lead::findOne(['id' => $form->leadId])) {
                throw new \DomainException('Not found Lead. ID: ' . $form->leadId);
            }

            //todo: validate can created user call from this lead?

            if (!$lead->project_id) {
                throw new \DomainException('Not found Project. Lead ID: ' . $lead->id);
            }

            if (!$lead->l_dep_id) {
                throw new \DomainException('Not found Department. Lead ID: ' . $lead->id);
            }

            $availablePhones = new AbacCallFromNumberList($form->createdUser, $lead);
            if (!$availablePhones->isExist($form->from)) {
                throw new \DomainException('Phone From (' . $form->from . ') is not available.');
            }

            $recordDisabled = (RecordManager::createCall(
                $form->createdUser->id,
                $lead->project_id,
                $lead->l_dep_id,
                $form->from,
                $lead->client_id
            ))->isDisabledRecord();

            $result = \Yii::$app->communication->createCall(
                new \src\model\call\useCase\conference\create\CreateCallForm([
                    'device' => $form->getVoipDevice(),
                    'user_id' => $form->getCreatedUserId(),
                    'to_number' => $form->to,
                    'from_number' => $form->from,
                    'phone_list_id' => $form->getPhoneListId(),
                    'project_id' => $lead->project_id,
                    'department_id' => $lead->l_dep_id,
                    'lead_id' => $lead->id,
                    'client_id' => $lead->client_id,
                    'source_type_id' => Call::SOURCE_LEAD,
                    'call_recording_disabled' => $recordDisabled,
                    'friendly_name' => FriendlyName::next(),
                    'project' => $lead->project_id ? $lead->project->name : '',
                    'source' => Call::SOURCE_LIST[Call::SOURCE_LEAD],
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

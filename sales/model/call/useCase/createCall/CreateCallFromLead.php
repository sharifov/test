<?php

namespace sales\model\call\useCase\createCall;

use common\models\Call;
use common\models\Lead;
use sales\auth\Auth;
use sales\helpers\UserCallIdentity;
use sales\model\call\services\FriendlyName;
use sales\model\call\services\RecordManager;
use sales\model\phone\AvailablePhoneList;

class CreateCallFromLead
{
    public function __invoke(CreateCallForm $form): array
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

            $departmentParams = $lead->lDep->getParams();
            if (!$departmentParams) {
                throw new \DomainException('Not found Department parameters. DepartmentId: ' . $lead->l_dep_id);
            }

            $availablePhones = new AvailablePhoneList($form->getCreatedUserId(), $lead->project_id, $lead->l_dep_id, $departmentParams->defaultPhoneType);
            if (!$availablePhones->isExist($form->from)) {
                throw new \DomainException('Phone From (' . $form->from . ') is not available.');
            }

            $recordDisabled = (RecordManager::createCall(
                Auth::id(),
                $lead->project_id,
                $lead->l_dep_id,
                $form->from,
                $lead->client_id
            ))->isDisabledRecord();

            $result = \Yii::$app->communication->createCall(
                new \sales\model\call\useCase\conference\create\CreateCallForm([
                    'user_identity' => $form->getDeviceIdentity(),
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

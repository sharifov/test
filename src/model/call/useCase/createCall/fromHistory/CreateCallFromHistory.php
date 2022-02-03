<?php

namespace src\model\call\useCase\createCall\fromHistory;

use common\models\Call;
use src\auth\Auth;
use src\model\call\services\FriendlyName;
use src\model\call\services\RecordManager;
use src\model\callLog\entity\callLog\CallLog;
use src\model\voip\phoneDevice\device\VoipDevice;

class CreateCallFromHistory
{
    public function __invoke(\src\model\call\useCase\createCall\CreateCallForm $form): array
    {
        try {
            if (!$call = CallLog::findOne(['cl_call_sid' => $form->historyCallSid])) {
                throw new \DomainException('Not found Call. History Call SID: ' . $form->historyCallSid);
            }

            //todo: validate can created user call from this history call?

            if (!$call->cl_project_id) {
                throw new \DomainException('Not found Project. History Call SID: ' . $form->historyCallSid);
            }

            if (!$call->cl_department_id) {
                throw new \DomainException('Not found Department. History Call SID: ' . $form->historyCallSid);
            }

            if (!$departmentParams = $call->department->getParams()) {
                throw new \DomainException('Not found Params. History Call SID: ' . $form->historyCallSid . ' Department: ' . $call->department->dep_name);
            }

            $phoneFrom = [
                'phone' => null,
                'phoneListId' => null
            ];

            if ($call->isOut()) {
                if (VoipDevice::isValid($call->cl_phone_from)) {
                    $list = new PhoneFromList(Auth::id(), $call->cl_project_id, $call->cl_department_id, $departmentParams->defaultPhoneType);
                    if ($firstPhone = $list->getFirst()) {
                        $phoneFrom = [
                            'phone' => $firstPhone->phone,
                            'phoneListId' => $firstPhone->phoneListId,
                        ];
                    }
                } else {
                    $phoneFrom = [
                        'phone' => $call->cl_phone_from,
                        'phoneListId' => $call->cl_phone_list_id,
                    ];
                }
            } elseif ($call->isIn()) {
                $list = new PhoneFromList(Auth::id(), $call->cl_project_id, $call->cl_department_id, $departmentParams->defaultPhoneType);
                if ($firstPhone = $list->getFirst()) {
                    $phoneFrom = [
                        'phone' => $firstPhone->phone,
                        'phoneListId' => $firstPhone->phoneListId,
                    ];
                }
            }

            if (!$phoneFrom['phone']) {
                throw new \DomainException('Phone From not found. History Call SID: ' . $form->historyCallSid);
            }

            $recordDisabled = (RecordManager::createCall(
                Auth::id(),
                $call->cl_project_id,
                $call->cl_department_id,
                $phoneFrom['phone'],
                $call->cl_client_id
            ))->isDisabledRecord();

            $sourceTypeId = null;
            $leadId = null;
            $caseId = null;

            if ($call->isOut()) {
                $sourceTypeId = $call->cl_category_id;
                $leadId = $call->callLogLead->cll_lead_id ?? null;
                $caseId = $call->callLogCase->clc_case_id ?? null;
            } elseif ($call->isIn()) {
                if ($call->cl_department_id) {
                    $departmentParams = $call->department->getParams();
                    if ($departmentParams) {
                        if ($departmentParams->object->type->isLead()) {
                            if (isset($call->callLogLead->cll_lead_id)) {
                                $sourceTypeId = Call::SOURCE_LEAD;
                                $leadId = $call->callLogLead->cll_lead_id;
                            }
                        } elseif ($departmentParams->object->type->isCase()) {
                            if (isset($call->callLogCase->clc_case_id)) {
                                $sourceTypeId = Call::SOURCE_CASE;
                                $caseId = $call->callLogCase->clc_case_id;
                            }
                        }
                    }
                }
            }

            $result = \Yii::$app->communication->createCall(
                new \src\model\call\useCase\conference\create\CreateCallForm([
                    'device' => $form->getVoipDevice(),
                    'user_id' => $form->getCreatedUserId(),
                    'to_number' => $form->to,
                    'from_number' => $phoneFrom['phone'],
                    'phone_list_id' => $phoneFrom['phoneListId'],
                    'project_id' => $call->cl_project_id,
                    'department_id' => $call->cl_department_id,
                    'lead_id' => $leadId,
                    'case_id' => $caseId,
                    'client_id' => $call->cl_client_id,
                    'source_type_id' => $sourceTypeId,
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

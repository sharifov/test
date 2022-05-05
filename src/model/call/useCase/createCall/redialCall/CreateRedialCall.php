<?php

namespace src\model\call\useCase\createCall\redialCall;

use common\models\Call;
use src\model\call\services\FriendlyName;
use src\model\call\services\RecordManager;
use src\model\leadRedial\queue\RedialCall;

class CreateRedialCall
{
    public function __invoke(RedialCall $redialCall, string $device): array
    {
        $recordDisabled = (RecordManager::createCall(
            $redialCall->userId,
            $redialCall->projectId,
            $redialCall->departmentId,
            $redialCall->phoneFrom,
            $redialCall->clientId
        ))->isDisabledRecord();

        return \Yii::$app->communication->createCall(
            new \src\model\call\useCase\conference\create\CreateCallForm([
                'device' => $device,
                'user_id' => $redialCall->userId,
                'to_number' => $redialCall->phoneTo,
                'from_number' => $redialCall->phoneFrom,
                'phone_list_id' => $redialCall->phoneListId,
                'project_id' => $redialCall->projectId,
                'department_id' => $redialCall->departmentId,
                'lead_id' => $redialCall->leadId,
                'client_id' => $redialCall->clientId,
                'source_type_id' => Call::SOURCE_REDIAL_CALL,
                'call_recording_disabled' => $recordDisabled,
                'friendly_name' => FriendlyName::next(),
                'is_redial_call' => true,
                'project' => $redialCall->projectName,
                'source' => Call::SOURCE_LIST[Call::SOURCE_REDIAL_CALL],
                'type' => Call::TYPE_LIST[Call::CALL_TYPE_OUT],
            ])
        );
    }
}

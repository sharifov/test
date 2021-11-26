<?php

namespace sales\model\call\useCase\createCall;

use common\models\Call;
use sales\helpers\UserCallIdentity;
use sales\model\call\services\FriendlyName;
use sales\model\call\services\RecordManager;
use sales\model\leadRedial\queue\RedialCall;

class CreateRedialCall
{
    public function __invoke(RedialCall $redialCall): array
    {
        $recordDisabled = (RecordManager::createCall(
            $redialCall->userId,
            $redialCall->projectId,
            $redialCall->departmentId,
            $redialCall->phoneFrom,
            $redialCall->clientId
        ))->isDisabledRecord();

        return \Yii::$app->communication->createCall(
            new \sales\model\call\useCase\conference\create\CreateCallForm([
                'user_identity' => UserCallIdentity::getClientId($redialCall->userId),
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
            ])
        );
    }
}

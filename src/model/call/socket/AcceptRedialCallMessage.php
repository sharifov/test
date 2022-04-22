<?php

namespace src\model\call\socket;

use common\models\Call;
use src\model\leadRedial\queue\RedialCall;

class AcceptRedialCallMessage
{
    public function create(RedialCall $call): array
    {
        return [
            'id' => null,
            'callSid' => null,
            'typeId' => Call::CALL_TYPE_OUT,
            'type' => Call::TYPE_LIST[Call::CALL_TYPE_OUT],
            'source_type_id' => Call::SOURCE_REDIAL_CALL,
            'source' => Call::SOURCE_LIST[Call::SOURCE_REDIAL_CALL],
            'project' => $call->projectName,
            'contact' => [
                'id' => $call->clientId,
                'name' => $call->clientName,
                'phone' => $call->phoneTo,
                'company' => '',
                'isClient' => $call->isClient,
                'callSid' => null,
            ],
            'department' => $call->departmentName,
        ];
    }
}

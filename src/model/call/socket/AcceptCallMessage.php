<?php

namespace src\model\call\socket;

use common\models\Call;
use common\models\Department;
use src\model\call\helper\CallHelper;

class AcceptCallMessage
{
    public function create(Call $call): array
    {
        if ($call->isJoin()) {
            $source = $call->c_parent_call_sid ? $call->cParent->getSourceName() : '';
        } else {
            $source = $call->getSourceName();
        }
        if ($source === '-') {
            $source = '';
        }
        $name =  $call->c_client_id ? $call->cClient->getShortName() : 'ClientName';
        $phone = $call->c_from;

        return [
            'id' => $call->c_id,
            'callSid' => $call->c_call_sid,
            'typeId' => $call->c_call_type_id,
            'type' => CallHelper::getTypeDescription($call),
            'source_type_id' => $call->c_source_type_id,
            'project' => $call->c_project_id ? $call->cProject->name : '',
            'source' => $source,
            'contact' => [
                'id' => $call->c_client_id,
                'name' => $name,
                'phone' => $phone,
                'company' => '',
                'isClient' => $call->c_client_id && $call->cClient->isClient(),
                'callSid' => $call->c_call_sid,
            ],
            'department' => $call->c_dep_id ? Department::getName($call->c_dep_id) : '',
        ];
    }
}

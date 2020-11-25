<?php

namespace sales\repositories\call;

use common\models\CallUserAccess;
use sales\repositories\NotFoundException;

class CallUserAccessRepository
{
    public function find(int $id): CallUserAccess
    {
        if ($callUserAccess = CallUserAccess::findOne($id)) {
            return $callUserAccess;
        }
        throw new NotFoundException('Call User Access is not found');
    }

    public function getByUserAndCallId(int $userId, int $callId): CallUserAccess
    {
        if ($callUserAccess = CallUserAccess::find()->where(['cua_user_id' => $userId, 'cua_call_id' => $callId, 'cua_status_id' => CallUserAccess::STATUS_TYPE_PENDING])->one()) {
            return $callUserAccess;
        }
        throw new NotFoundException('Call User Access is not found');
    }

    public function save(CallUserAccess $callUserAccess): CallUserAccess
    {
        if (!$callUserAccess->save()) {
            throw new \RuntimeException($callUserAccess->getErrorSummary(false)[0]);
        }
        return $callUserAccess;
    }
}

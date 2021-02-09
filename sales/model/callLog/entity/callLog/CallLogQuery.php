<?php

namespace sales\model\callLog\entity\callLog;

use sales\model\callLog\entity\callLogRecord\CallLogRecord;

class CallLogQuery
{
    /**
     * @param string $callSid
     * @return array
     */
    public static function getCallLogRecordByCallSid(string $callSid): ?array
    {
        return CallLog::find()
            ->select(['clr_record_sid', 'clr_duration'])
            ->where(['cl_call_sid' => $callSid])
            ->innerJoin(CallLogRecord::tableName(), 'cl_id = clr_cl_id')
            ->asArray()
            ->one();
    }
}

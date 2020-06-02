<?php

namespace frontend\widgets\newWebPhone\call;

use common\models\Call;

class CallHelper
{
    public static function getTypeDescription(Call $call): string
    {
        $description = '';
        if ($call->isIn()) {
            $description = 'Incoming';
        } elseif ($call->isOut()) {
            $description = 'Outgoing';
        } elseif ($call->isJoin()) {
            $description = 'Join';
            if ($call->c_source_type_id === Call::SOURCE_LISTEN) {
                $description .= ': Listen';
            } elseif ($call->c_source_type_id === Call::SOURCE_COACH) {
                $description .= ': Coach';
            } elseif ($call->c_source_type_id === Call::SOURCE_BARGE) {
                $description .= ': Barge';
            }
        }
        return $description;
    }
}

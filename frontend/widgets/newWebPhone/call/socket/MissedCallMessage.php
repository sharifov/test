<?php

namespace frontend\widgets\newWebPhone\call\socket;

use common\models\Call;

class MissedCallMessage
{
    public const COMMAND = 'missedCall';
    public const COMMAND_ADD = 'add_missed_call';
    public const COMMAND_UPDATE_COUNT = 'update_count_missed_calls';

    public static function add(Call $call): array
    {
        return [
            'data' => [
                'command' => self::COMMAND_ADD,
                'call' => [
                    'id' => $call->c_id,
                    'sid' => $call->c_call_sid,
                    'user_id' => $call->c_created_user_id,
                ],
            ],
        ];
    }

    public static function updateCount(int $count): array
    {
        return [
            'data' => [
                'command' => self::COMMAND_UPDATE_COUNT,
                'count' => $count,
            ],
        ];
    }
}

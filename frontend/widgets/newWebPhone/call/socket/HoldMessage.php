<?php

namespace frontend\widgets\newWebPhone\call\socket;

use common\models\Call;

class HoldMessage
{
    public const COMMAND = 'holdCall';
    public const COMMAND_HOLD = 'hold';
    public const COMMAND_UNHOLD = 'unhold';

    public static function hold(Call $call): array
    {
        return [
            'data' => [
                'command' => self::COMMAND_HOLD,
                'call' => [
                    'id' => $call->c_id,
                    'sid' => $call->c_call_sid,
                    'user_id' => $call->c_created_user_id,
                    'type_id' => $call->c_call_type_id,
                ],
            ],
        ];
    }

    public static function unhold(Call $call): array
    {
        return [
            'data' => [
                'command' => self::COMMAND_UNHOLD,
                'call' => [
                    'id' => $call->c_id,
                    'sid' => $call->c_call_sid,
                    'user_id' => $call->c_created_user_id,
                    'type_id' => $call->c_call_type_id,
                ],
            ],
        ];
    }
}

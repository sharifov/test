<?php

namespace frontend\widgets\newWebPhone\call\socket;

use common\models\Call;

class MuteMessage
{
    public const COMMAND = 'muteCall';
    public const COMMAND_MUTE = 'mute';
    public const COMMAND_UNMUTE = 'unmute';

    public static function mute(Call $call): array
    {
        return [
            'data' => [
                'command' => self::COMMAND_MUTE,
                'call' => [
                    'id' => $call->c_id,
                    'sid' => $call->c_call_sid,
                    'user_id' => $call->c_created_user_id,
                ],
            ],
        ];
    }

    public static function unmute(Call $call): array
    {
        return [
            'data' => [
                'command' => self::COMMAND_UNMUTE,
                'call' => [
                    'id' => $call->c_id,
                    'sid' => $call->c_call_sid,
                    'user_id' => $call->c_created_user_id,
                ],
            ],
        ];
    }
}

<?php

namespace frontend\widgets\newWebPhone\call\socket;

class RemoveIncomingRequestMessage
{
    public const COMMAND = 'removeIncomingRequest';

    public static function create(int $callId, string $callSid): array
    {
        return [
            'data' => [
                'call' => [
                    'id' => $callId,
                    'sid' => $callSid,
                ],
            ],
        ];
    }
}

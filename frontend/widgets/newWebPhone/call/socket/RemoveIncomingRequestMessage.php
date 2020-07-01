<?php

namespace frontend\widgets\newWebPhone\call\socket;

class RemoveIncomingRequestMessage
{
    public const COMMAND = 'removeIncomingRequest';

    public static function create(string $callSid): array
    {
        return [
            'data' => [
                'call' => [
                    'sid' => $callSid,
                ],
            ],
        ];
    }
}

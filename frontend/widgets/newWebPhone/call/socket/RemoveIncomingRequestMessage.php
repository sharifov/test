<?php

namespace frontend\widgets\newWebPhone\call\socket;

class RemoveIncomingRequestMessage
{
    public const COMMAND = 'removeIncomingRequest';

    public static function create(int $callId): array
    {
        return [
            'data' => [
                'call' => [
                    'id' => $callId,
                ],
            ],
        ];
    }
}

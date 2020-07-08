<?php

namespace sales\model\conference\socket;

use common\models\Notifications;

class SocketCommands
{
    public static function sendToAllUsers(array $data): void
    {
        foreach ($data['users'] as $userId) {
            $participants = [];
            foreach ($data['participants'] as $key => $part) {
                if (!$part['userId'] || $part['userId'] === $userId) {
                    unset($part['userId']);
                    $participants[] = $part;
                }
            }

            Notifications::publish('conferenceUpdate', ['user_id' => $userId], [
                'data' => [
                    'command' => 'conferenceUpdate',
                    'conference' => [
                        'sid' => $data['conference']['sid'],
                        'duration' => $data['conference']['duration'],
                        'participants' => $participants,
                    ],
                ]
            ]);
        }
    }
}

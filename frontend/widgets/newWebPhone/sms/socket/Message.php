<?php

namespace frontend\widgets\newWebPhone\sms\socket;

use common\models\Client;
use common\models\Employee;
use common\models\Sms;
use frontend\widgets\newWebPhone\sms\dto\SmsDto;

class Message
{
    public const COMMAND_UPDATE_STATUS = 'update_status';
    public const COMMAND_ADD = 'add';

    public static function updateStatus(Sms $sms): array
    {
        return [
            'data' => [
                'command' => self::COMMAND_UPDATE_STATUS,
                'sms' => [
                    'id' => $sms->s_id,
                    'status' => $sms->s_status_id,
                ],
            ],
        ];
    }

    public static function add(Sms $sms, Employee $user, Client $contact): array
    {
        return [
            'data' => [
                'command' => self::COMMAND_ADD,
                'sms' => (new SmsDto($sms, $user, $contact))->toArray(),
                'contact' => [
                    'id' => $contact->id,
                    'name' => $contact->getNameByType(),
                    'phone' => $sms->isOut() ? $sms->s_phone_to : $sms->s_phone_from,
                ],
                'user' => [
                    'phone' => $sms->isOut() ? $sms->s_phone_from : $sms->s_phone_to,
                ],
            ],
        ];
    }
}

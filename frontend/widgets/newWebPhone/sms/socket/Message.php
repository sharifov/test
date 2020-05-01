<?php

namespace frontend\widgets\newWebPhone\sms\socket;

use common\models\Client;
use common\models\Employee;
use common\models\Sms;
use frontend\widgets\newWebPhone\sms\dto\SmsDto;

class Message
{
    public static function updateStatus(Sms $sms): array
    {
        return [
            'sms' => [
                'id' => $sms->s_id,
                'status' => $sms->s_status_id,
            ]
        ];
    }

    public static function add(Sms $sms, Employee $user, Client $contact): array
    {
        return [
            'sms' => (new SmsDto($sms, $user, $contact))->toArray(),
            'contact' => [
                'id' => $contact->id,
                'name' => $contact->getNameByType(),
                'phone' => $sms->isOut() ? $sms->s_phone_to : $sms->s_phone_from,
            ],
            'userPhone' => $sms->isOut() ? $sms->s_phone_from : $sms->s_phone_to,
        ];
    }
}

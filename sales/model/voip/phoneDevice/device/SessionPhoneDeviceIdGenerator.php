<?php

namespace sales\model\voip\phoneDevice;

class SessionPhoneDeviceIdGenerator implements PhoneDeviceIdGenerator
{
    public function getId(int $userId): string
    {
        if ($currentId = \Yii::$app->session->get('deviceId')) {
            $deviceId = $currentId;
        } else {
            $deviceId = random_int(0, 100);
            \Yii::$app->session->set('deviceId', $deviceId);
        }
    }
}

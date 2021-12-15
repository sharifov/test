<?php

namespace sales\helpers;

use common\models\Employee;
use sales\model\voip\phoneDevice\device\VoipDevice;

class PhoneFormatter
{
    public static function getPhoneOrNickname(?string $phone): ?string
    {
        if (VoipDevice::isValid($phone) && ($userId = VoipDevice::getUserId($phone)) && ($user = Employee::find()->select(['nickname'])->andWhere(['id' => $userId])->asArray()->one())) {
            return $user['nickname'];
        }
        return $phone;
    }
}

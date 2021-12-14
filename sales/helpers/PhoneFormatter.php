<?php

namespace sales\helpers;

use common\models\Employee;
use sales\model\voip\phoneDevice\device\PhoneDeviceIdentity;

class PhoneFormatter
{
    public static function getPhoneOrNickname(?string $phone): ?string
    {
        if (PhoneDeviceIdentity::canParse($phone) && ($userId = PhoneDeviceIdentity::getUserId($phone)) && ($user = Employee::find()->select(['nickname'])->andWhere(['id' => $userId])->asArray()->one())) {
            return $user['nickname'];
        }
        return $phone;
    }
}

<?php

namespace sales\helpers;

use common\models\Employee;

class PhoneFormatter
{
    public static function getPhoneOrNickname(?string $phone): ?string
    {
        if (UserCallIdentity::canParse($phone) && ($userId = UserCallIdentity::parseUserId($phone)) && ($user = Employee::find()->select(['nickname'])->andWhere(['id' => $userId])->asArray()->one())) {
            return $user['nickname'];
        }
        return $phone;
    }
}

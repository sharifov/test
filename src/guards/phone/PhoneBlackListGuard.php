<?php

namespace src\guards\phone;

use src\auth\Auth;
use src\helpers\setting\SettingHelper;

class PhoneBlackListGuard
{
    public static function canAdd(int $userId): bool
    {
        $auth = \Yii::$app->authManager;

        return SettingHelper::isPhoneBlacklistEnabled() && $auth->checkAccess($userId, 'PhoneWidget_AddBlockList');
    }
}

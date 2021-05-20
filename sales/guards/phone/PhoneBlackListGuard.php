<?php

namespace sales\guards\phone;

use sales\auth\Auth;
use sales\helpers\setting\SettingHelper;

class PhoneBlackListGuard
{
    public static function canAdd(int $userId): bool
    {
        $auth = \Yii::$app->authManager;

        return SettingHelper::isPhoneBlacklistEnabled() && $auth->checkAccess($userId, 'PhoneWidget_AddBlockList');
    }
}

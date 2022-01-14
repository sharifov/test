<?php

namespace src\guards\call;

use src\helpers\setting\SettingHelper;

class CallRedialGuard
{
    public static function guard(string $projectKey, string $departmentKey): bool
    {
        return SettingHelper::isCallbackToCallerEnabled() &&
            (!in_array($projectKey, SettingHelper::getCallbackToCallerExcludedProjectList(), true) &&
            !in_array($departmentKey, SettingHelper::getCallbackToCallerExcludedDepartmentList(), true));
    }
}

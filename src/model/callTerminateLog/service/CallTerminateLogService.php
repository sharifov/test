<?php

namespace src\model\callTerminateLog\service;

use common\models\Call;
use src\helpers\setting\SettingHelper;
use src\model\callTerminateLog\entity\CallTerminateLog;

/**
 * Class CallTerminateLogService
 */
class CallTerminateLogService
{
    public static function isPhoneBlackListCandidate(string $phone): bool
    {
        $limitMinute = (int) (SettingHelper::getCallTerminateBlackListByKey('limit_minutes') ?? 15);
        $limitCount = (int) (SettingHelper::getCallTerminateBlackListByKey('limit_count') ?? 2);
        $countIvr = self::getCntIvrByPhoneAndLimitMinute($phone, $limitMinute);

        return ($countIvr > $limitCount);
    }

    public static function getCntIvrByPhoneAndLimitMinute(string $phone, int $limitMinute): int
    {
        return (int) CallTerminateLog::find()
            ->where(['ctl_call_phone_number' => $phone])
            ->andWhere(['ctl_call_status_id' => Call::STATUS_IVR])
            ->andWhere(['>=', 'ctl_created_dt', self::minuteToDt($limitMinute)])
            ->count();
    }

    private static function minuteToDt(int $minute, string $format = 'Y-m-d H:i:s'): string
    {
        return (new \DateTime('now'))
                ->modify('-' . $minute . ' minutes')
                ->format($format);
    }
}

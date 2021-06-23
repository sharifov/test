<?php

namespace sales\services\phone\blackList;

use common\models\PhoneBlacklist;
use common\models\PhoneBlacklistLog;
use sales\helpers\setting\SettingHelper;

class PhoneBlackListManageService
{
    public function add(string $phone, \DateTime $date): PhoneBlacklist
    {
        $phoneBlackListLog = new PhoneBlacklistLog();
        $phoneBlackListLog->pbll_phone = $phone;
        if (!$phoneBlackListLog->save()) {
            throw new \RuntimeException($phoneBlackListLog->getErrorSummary(true)[0]);
        }

        $blackListLogCount = PhoneBlacklistLog::find()->byPhone($phone)->byMinutesPeriod(SettingHelper::getPhoneBlacklistLastTimePeriod())->count();

        $addMinutes = SettingHelper::getPhoneBlacklistPeriodByIndex($blackListLogCount);
        $date->add(new \DateInterval("PT{$addMinutes}M"));

        $phoneBlackList = PhoneBlacklist::create($phone, $date->format('Y-m-d H:i:s'));
        $phoneBlackList->scenario = PhoneBlacklist::SCENARIO_INSERT;
        if (!$phoneBlackList->save()) {
            throw new \RuntimeException($phoneBlackList->getErrorSummary(true)[0]);
        }

        return $phoneBlackList;
    }

    public function enableWithExpiredDateTime(PhoneBlacklist $phoneBlackList, \DateTime $date): void
    {
        $phoneBlackListLog = new PhoneBlacklistLog();
        $phoneBlackListLog->pbll_phone = $phoneBlackList->pbl_phone;
        if (!$phoneBlackListLog->save()) {
            throw new \RuntimeException($phoneBlackListLog->getErrorSummary(true)[0]);
        }

        $blackListLogCount = PhoneBlacklistLog::find()->byPhone($phoneBlackList->pbl_phone)->byMinutesPeriod(SettingHelper::getPhoneBlacklistLastTimePeriod())->count();

        $addMinutes = SettingHelper::getPhoneBlacklistPeriodByIndex($blackListLogCount);
        $date->add(new \DateInterval("PT{$addMinutes}M"));
        $phoneBlackList->pbl_expiration_date = $date->format('Y-m-d H:i:s');
        $phoneBlackList->pbl_enabled = true;
        if (!$phoneBlackList->save()) {
            throw new \RuntimeException($phoneBlackList->getErrorSummary(true)[0]);
        }
    }

    public static function createOrRenewExpiration(string $phone, int $addMinutes, \DateTime $date, ?string $description = null): PhoneBlacklist
    {
        $date->add(new \DateInterval("PT{$addMinutes}M"));
        $expirationDate = $date->format('Y-m-d H:i:s');

        if ($phoneBlackList = PhoneBlacklist::findOne(['pbl_phone' => $phone])) {
            $phoneBlackList->pbl_expiration_date = $expirationDate;
            $phoneBlackList->pbl_enabled = true;
            $phoneBlackList->pbl_description = $description;
        } else {
            $phoneBlackList = PhoneBlacklist::create($phone, $expirationDate, $description);
        }

        if (!$phoneBlackList->save()) {
            throw new \RuntimeException($phoneBlackList->getErrorSummary(true)[0]);
        }

        $phoneBlackListLog = new PhoneBlacklistLog();
        $phoneBlackListLog->pbll_phone = $phone;
        if (!$phoneBlackListLog->save()) {
            throw new \RuntimeException($phoneBlackListLog->getErrorSummary(true)[0]);
        }

        return $phoneBlackList;
    }
}

<?php

namespace sales\model\voip\phoneDevice\log;

class PhoneDeviceLogCleaner
{
    public function clean(\DateTimeImmutable $to): int
    {
        return PhoneDeviceLog::deleteAll(['<', 'pdl_created_dt', $to->format('Y-m-d')]);
    }
}

<?php

namespace sales\model\voip\phoneDevice\device;

class PhoneDeviceCleaner
{
    public function clean(\DateTimeImmutable $to): int
    {
        return PhoneDevice::deleteAll(['<', 'pd_created_dt', $to->format('Y-m-d')]);
    }
}

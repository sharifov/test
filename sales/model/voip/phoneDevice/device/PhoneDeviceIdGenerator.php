<?php

namespace sales\model\voip\phoneDevice;

interface PhoneDeviceIdGenerator
{
    public function getId(int $userId): string;
}

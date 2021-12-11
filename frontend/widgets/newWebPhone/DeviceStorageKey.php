<?php

namespace frontend\widgets\newWebPhone;

class DeviceStorageKey
{
    public static function getPhoneDeviceIdStorageKey(int $userId): string
    {
        return 'PhoneDeviceId' . $userId;
    }
}

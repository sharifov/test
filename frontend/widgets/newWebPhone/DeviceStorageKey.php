<?php

namespace frontend\widgets\newWebPhone;

class DeviceStorageKey
{
    public static function getDeviceIdStorageKey(int $userId): string
    {
        return 'phoneDeviceId' . $userId;
    }
}

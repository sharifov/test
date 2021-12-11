<?php

namespace sales\model\voip\phoneDevice\device;

class PhoneDeviceIdentity
{
    public function get(int $deviceId, int $userId): string
    {
        $device = PhoneDevice::find()->byId($deviceId)->one();
        if (!$device) {
            throw new \DomainException('Not found device. Id (' . $deviceId . ')');
        }
        if (!$device->isEqualUser($userId)) {
            throw new \DomainException('Device (' . $deviceId . ') is invalid. Error relation with user(' . $userId . ').');
        }
        if (!$device->isReady()) {
            throw new \DomainException('Device is not ready. Please refresh Voip page.');
        }
        return $device->getClientDeviceIdentity();
    }
}

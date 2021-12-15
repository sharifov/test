<?php

namespace sales\model\voip\phoneDevice\device;

use common\models\Employee;

class ReadyVoipDevice
{
    public function find(int $deviceId, int $userId): string
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

        return $device->getVoipDevice();
    }

    public function findAny(Employee $user): string
    {
        $device = PhoneDevice::find()->byUserId($user->id)->ready()->one();

        if (!$device) {
            throw new \DomainException('User ' . ($user->nickname ?: $user->full_name) . ' is not ready');
        }

        return $device->getVoipDevice();
    }
}

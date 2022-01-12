<?php

namespace src\model\voip\phoneDevice\device;

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
        $device = PhoneDevice::find()->byUserId($user->id)->ready()->limit(1)->one();

        if (!$device) {
            throw new \DomainException('User ' . ($user->nickname ?: $user->full_name) . ' is not ready');
        }

        return $device->getVoipDevice();
    }

    public function findAnyId(int $userId): ?int
    {
        $device = PhoneDevice::find()->select(['pd_id'])->byUserId($userId)->ready()->asArray()->limit(1)->one();

        if ($device) {
            return (int)$device['pd_id'];
        }

        return null;
    }

    public function findBrowserGroupIds(int $userId): array
    {
        return array_map('intval', PhoneDevice::find()->select(['min(pd_id)'])->byUserId($userId)->ready()->groupBy(['pd_user_agent'])->asArray()->column());
    }
}

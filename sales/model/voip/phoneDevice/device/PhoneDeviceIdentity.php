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

    public static function generate(int $userId, string $postFix): string
    {
        return \Yii::$app->params['appEnv'] . 'user' . $userId . '_' . $postFix;
    }

    public static function getPrefix(): string
    {
        return 'client:';
    }

    public static function canParse(?string $value): bool
    {
        if (!$value) {
            return false;
        }
        return strpos($value, self::getPrefix()) === 0;
    }

    public static function getUserId(string $value): int
    {
        return (int)preg_replace('/[^0-9]/', '', $value);
    }
}

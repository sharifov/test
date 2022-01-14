<?php

namespace src\model\userModelSetting\service;

use src\model\userModelSetting\entity\UserModelSetting;
use src\model\userModelSetting\entity\UserModelSettingScopes;
use src\model\userModelSetting\repository\UserModelSettingRepository;

/**
 * Class UserModelSettingService
 */
class UserModelSettingService
{
    public static function getOrCreate(
        int $userId,
        string $class,
        array $fields,
        string $name = UserModelSetting::DEFAULT_NAME
    ): UserModelSetting {
        if (!$userModelSetting = self::findLastByUserAndClass($userId, $class, $name)) {
            $userModelSetting = UserModelSetting::create($userId, $class, ['fields' => $fields], $name);
            (new UserModelSettingRepository())->save($userModelSetting);
        }
        return $userModelSetting;
    }

    public static function findLastByUserAndClass(
        int $userId,
        string $class,
        string $name = UserModelSetting::DEFAULT_NAME
    ): ?UserModelSetting {
        /** @var UserModelSetting $userModelSetting */
        $userModelSetting = UserModelSetting::find()
            ->byUserId($userId)
            ->byClass($class)
            ->byName($name)
            ->last()
            ->one();
        return $userModelSetting;
    }

    public static function getFields(int $userId, string $class): array
    {
        if ($userModelSetting = self::findLastByUserAndClass($userId, $class)) {
            return $userModelSetting->getFields();
        }
        return [];
    }
}

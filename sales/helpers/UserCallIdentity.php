<?php

namespace sales\helpers;

class UserCallIdentity
{
    private static function getOldPrefix(): string
    {
        return 'seller';
    }

    private static function getPrefix(): string
    {
        if (!empty(\Yii::$app->params['appEnv'])) {
            return \Yii::$app->params['appEnv'] . 'user';
        }
        return self::getOldPrefix();
    }

    public static function getClientPrefix(): string
    {
        return 'client:';
    }

    public static function getFullPrefix(): string
    {
        return self::getClientPrefix() . self::getPrefix();
    }

    public static function getOldFullPrefix(): string
    {
        return self::getClientPrefix() . self::getOldPrefix();
    }

    public static function getId(int $userId): string
    {
        return self::getPrefix() . $userId;
    }

    public static function getClientId(int $userId): string
    {
        return self::getFullPrefix() . $userId;
    }

    public static function parseUserId(string $str): int
    {
        if ($userId = (int)str_replace(self::getFullPrefix(), '', $str)) {
            return $userId;
        }
        return (int)str_replace(self::getOldFullPrefix(), '', $str);
    }

    public static function canParse(?string $str): bool
    {
        if (!$str) {
            return false;
        }
        return strpos($str, self::getFullPrefix()) === 0 || strpos($str, self::getOldFullPrefix()) === 0;
    }
}

<?php

namespace sales\helpers;

class UserCallIdentity
{
    private static function getPrefix(): string
    {
        if (!empty(\Yii::$app->params['appEnv'])) {
            return \Yii::$app->params['appEnv'] . 'user';
        }
        return 'seller';
    }

    public static function getClientPrefix(): string
    {
        return 'client:';
    }

    public static function getFullPrefix(): string
    {
        return self::getClientPrefix() . self::getPrefix();
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
        return (int)str_replace(self::getFullPrefix(), '', $str);
    }

    public static function canParse(?string $str): bool
    {
        if (!$str) {
            return false;
        }
        return strpos($str, UserCallIdentity::getFullPrefix()) === 0;
    }
}

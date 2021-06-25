<?php

namespace sales\model\clientChat\componentEvent\component\defaultConfig;

use frontend\helpers\JsonHelper;

class FlizzardSubscriptionDefaultConfig implements ComponentEventDefaultConfig
{
    private static array $config = [];

    public static function getConfig(): array
    {
        return self::$config;
    }

    public static function getConfigJson(): string
    {
        return JsonHelper::encode(self::$config);
    }
}

<?php

namespace src\model\clientChat\componentRule\component\defaultConfig;

use frontend\helpers\JsonHelper;

class SendMessageDefaultConfig implements ComponentRuleDefaultConfig
{
    private static array $config = [
        'message' => 'You do not have a subscription to chat with an agent'
    ];

    public static function getConfig(): array
    {
        return self::$config;
    }

    public static function getConfigJson(): string
    {
        return JsonHelper::encode(self::$config);
    }
}

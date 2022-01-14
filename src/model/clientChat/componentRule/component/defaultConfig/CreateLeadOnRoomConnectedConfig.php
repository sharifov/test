<?php

namespace src\model\clientChat\componentRule\component\defaultConfig;

use frontend\helpers\JsonHelper;

class CreateLeadOnRoomConnectedConfig implements ComponentRuleDefaultConfig
{
    private static array $config = [
        'create_lead_if_already_exists' => true,
        'add_top_quotes' => [
            'enabled' => true,
            'count' => 3
        ]
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

<?php

namespace sales\model\clientChat\componentRule\component\defaultConfig;

use frontend\helpers\JsonHelper;

class CreateLeadOnRoomConnectedConfig implements ComponentRuleDefaultConfig
{
    private static array $config = [
        'create_lead_if_already_exists' => true
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

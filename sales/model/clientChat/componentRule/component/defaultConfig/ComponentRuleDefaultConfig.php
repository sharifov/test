<?php

namespace sales\model\clientChat\componentRule\component\defaultConfig;

interface ComponentRuleDefaultConfig
{
    public static function getConfig(): array;

    public static function getConfigJson(): string;
}

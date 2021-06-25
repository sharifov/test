<?php

namespace sales\model\clientChat\componentEvent\component\defaultConfig;

interface ComponentEventDefaultConfig
{
    public static function getConfig(): array;

    public static function getConfigJson(): string;
}

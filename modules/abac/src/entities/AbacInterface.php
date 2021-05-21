<?php

namespace modules\abac\src\entities;

/**
 * Interface AbacInterface
 * @package modules\abac\src\entities
 */
interface AbacInterface
{
    public static function getObjectList(): array;
    public static function getObjectActionList(): array;
    public static function getObjectAttributeList(): array;
}

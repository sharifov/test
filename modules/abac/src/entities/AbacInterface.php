<?php

namespace modules\abac\src\entities;

/**
 * Interface AbacInterface
 * @package modules\abac\src\entities
 */
interface AbacInterface
{
    public static function getObjectList(): array;
    // public function getActionListByObject($object): array;
   // public function getAttributeListByObject($object): array;
}

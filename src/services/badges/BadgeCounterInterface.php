<?php

namespace src\services\badges;

/**
 * Class BadgeCounterInterface
 */
interface BadgeCounterInterface
{
    public function countTypes(array $types): array;
}

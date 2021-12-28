<?php

namespace sales\services\badges;

/**
 * Class BadgeCounterInterface
 */
interface BadgeCounterInterface
{
    public function countTypes(array $types): array;
}

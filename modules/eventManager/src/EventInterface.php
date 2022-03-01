<?php

namespace modules\eventManager\src;

/**
 * Interface EventInterface
 * @package modules\eventManager\src
 */
interface EventInterface
{
    public static function getName(): string;
    public static function getEventList(): array;
    public static function getHandlerList(): array;
}

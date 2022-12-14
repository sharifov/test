<?php

namespace modules\taskList\src\objects;

/**
 * Interface TaskObjectInterface
 * @package modules\taskList\src\objects
 */
interface TaskObjectInterface
{
    public static function getObjectAttributeList(): array;
    public static function getObjectOptionList(): array;
    public static function getTargetObjectList(): array;
}

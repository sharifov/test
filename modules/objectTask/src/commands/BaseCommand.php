<?php

namespace modules\objectTask\src\commands;

use modules\objectTask\src\entities\ObjectTask;

abstract class BaseCommand
{
    public ObjectTask $objectTask;
    public array $config;

    public function __construct(ObjectTask $objectTask, array $config)
    {
        $this->objectTask = $objectTask;
        $this->config = $config;
    }

    abstract public static function getConfigTemplate(): array;

    abstract public function process(): bool;
}

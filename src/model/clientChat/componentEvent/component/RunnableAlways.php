<?php

namespace src\model\clientChat\componentEvent\component;

use src\model\clientChat\componentEvent\component\defaultConfig\DefaultConfig;

class RunnableAlways implements ComponentEventInterface
{
    public function run(ComponentDTOInterface $dto): string
    {
        return 'true';
    }

    public function getDefaultConfig(): array
    {
        return DefaultConfig::getConfig();
    }

    public function getDefaultConfigJson(): string
    {
        return DefaultConfig::getConfigJson();
    }
}

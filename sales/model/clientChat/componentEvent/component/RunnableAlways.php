<?php

namespace sales\model\clientChat\componentEvent\component;

use sales\model\clientChat\componentEvent\component\defaultConfig\DefaultConfig;

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

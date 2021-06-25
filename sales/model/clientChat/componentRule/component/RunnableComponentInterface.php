<?php

namespace sales\model\clientChat\componentRule\component;

use sales\model\clientChat\componentEvent\component\ComponentDTOInterface;

interface RunnableComponentInterface
{
    public function run(ComponentDTOInterface $dto): void;

    public function getDefaultConfig(): array;

    public function getDefaultConfigJson(): string;
}

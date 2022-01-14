<?php

namespace src\model\clientChat\componentRule\component;

use src\model\clientChat\componentEvent\component\ComponentDTOInterface;

interface RunnableComponentInterface
{
    public function run(ComponentDTOInterface $dto): void;

    public function getDefaultConfig(): array;

    public function getDefaultConfigJson(): string;
}

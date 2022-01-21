<?php

namespace src\model\clientChat\componentEvent\component;

interface ComponentEventInterface
{
    public function run(ComponentDTOInterface $dto): string;

    public function getDefaultConfig(): array;

    public function getDefaultConfigJson(): string;
}

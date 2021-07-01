<?php

namespace sales\model\clientChat\componentEvent\component;

interface ComponentEventInterface
{
    public function run(ComponentDTOInterface $dto): string;

    public function getDefaultConfig(): array;

    public function getDefaultConfigJson(): string;
}

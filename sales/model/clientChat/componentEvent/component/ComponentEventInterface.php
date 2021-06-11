<?php

namespace sales\model\clientChat\componentEvent\component;

interface ComponentEventInterface
{
    public function run(ComponentDTOInterface $dto): string;
}

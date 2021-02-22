<?php

namespace sales\model\voip\command\types\queue;

use sales\model\voip\command\types\Executable;

class Command implements Executable
{

    private function __construct()
    {
    }

    public function fromArray(): self
    {
    }

    public function toArray(): array
    {
        return [];
    }
}

<?php

namespace sales\model\call\entity\call;

class Data
{
    public function __construct(?string $json)
    {
        if ($json === null) {
            return;
        }
    }
}

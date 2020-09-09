<?php

namespace sales\model\conference\entity\aggregate\log;

interface Log
{
    public function isEvent(): bool;
    public function isParticipants(): bool;
    public function isResult(): bool;
}

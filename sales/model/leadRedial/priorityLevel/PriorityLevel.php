<?php

namespace sales\model\leadRedial\priorityLevel;

interface PriorityLevel
{
    public function calculate(float $percent): int;
}

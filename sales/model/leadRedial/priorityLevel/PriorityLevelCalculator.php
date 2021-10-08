<?php

namespace sales\model\leadRedial\priorityLevel;

interface PriorityLevelCalculator
{
    public function calculate(float $percent): int;
}

<?php

namespace src\model\leadRedial\priorityLevel;

interface PriorityLevelCalculator
{
    public function calculate(float $percent): int;
}

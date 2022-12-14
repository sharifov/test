<?php

namespace src\helpers;

class LogExecutionTime
{
    private $startLogTime;

    private $endLogTime;

    private array $actionList = [];

    private string $currentAction = '';

    public function start(string $action): self
    {
        if ($this->currentAction) {
            $this->end();
        }
        $this->currentAction = $action;
        $this->startLogTime = microtime(true);
        return $this;
    }

    public function end(): self
    {
        if ($this->startLogTime) {
            $this->endLogTime = number_format(round(microtime(true) - $this->startLogTime, 2), 2);
            $this->actionList[$this->currentAction] = $this->endLogTime;
            $this->resetCurrentLog();
        }
        return $this;
    }

    public function resetCurrentLog(): void
    {
        $this->startLogTime = null;
        $this->currentAction = '';
        $this->endLogTime = null;
    }

    public function getResult(): array
    {
        return $this->actionList;
    }

    public function getByAction(string $action): float
    {
        return (float)($this->actionList[$action] ?? null);
    }

    public function calcTotalExecTime(): float
    {
        $total = 0.0;
        foreach ($this->actionList as $actionTime) {
            $total += (float)$actionTime;
        }
        return $total;
    }
}

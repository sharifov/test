<?php

namespace common\components\debug;

class Timer
{
    private $timers = [];

    public function start(string $key): void
    {
        if (array_key_exists($key, $this->timers)) {
            throw new \InvalidArgumentException('Key ' . $key . ' is already exist.');
        }
        $this->timers[$key] = microtime(true);
    }

    public function stop(string $key): string
    {
        if (!array_key_exists($key, $this->timers)) {
            throw new \InvalidArgumentException('Key ' . $key . ' not found.');
        }
        $finish = number_format(round(microtime(true) - $this->timers[$key], 2), 2);
        unset($this->timers[$key]);
        return $finish;
    }
}

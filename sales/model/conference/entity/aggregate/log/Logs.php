<?php

namespace sales\model\conference\entity\aggregate\log;

/**
 * Class Logs
 *
 * @property Log[] $logs
 */
class Logs
{
    public array $logs;

    public function add(Log $log): void
    {
        $this->logs[] = $log;
    }
}

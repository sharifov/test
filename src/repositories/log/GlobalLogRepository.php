<?php

namespace src\repositories\log;

use src\entities\log\GlobalLog;

/**
 * Class GlobalLogsRepository
 * @package src\repositories\logs
 */
class GlobalLogRepository
{
    /**
     * @param GlobalLog $globalLog
     * @return int
     */
    public function save(GlobalLog $globalLog): int
    {
        if (!$globalLog->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        return $globalLog->gl_id;
    }
}

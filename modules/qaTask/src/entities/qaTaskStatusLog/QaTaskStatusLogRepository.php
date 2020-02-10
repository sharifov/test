<?php

namespace modules\qaTask\src\entities\qaTaskStatusLog;

use modules\qaTask\src\exceptions\QaTaskCodeException;

/**
 * Class QaTaskStatusLogRepository
 */
class QaTaskStatusLogRepository
{
    public function getPrevious(int $taskId): ?QaTaskStatusLog
    {
        if ($log = QaTaskStatusLog::find()->last($taskId)->one()) {
            return $log;
        }
        return null;
    }

    public function save(QaTaskStatusLog $log): int
    {
        if (!$log->save(false)) {
            throw new \RuntimeException('Saving error', QaTaskCodeException::QA_TASK_STATUS_LOG_SAVE);
        }
        return $log->tsl_id;
    }

    public function remove(QaTaskStatusLog $log): void
    {
        if (!$log->delete()) {
            throw new \RuntimeException('Removing error', QaTaskCodeException::QA_TASK_STATUS_LOG_REMOVE);
        }
    }
}

<?php

namespace modules\qaTask\src\entities\qaTaskStatus;

use modules\qaTask\src\exceptions\QaTaskCodeException;
use sales\repositories\NotFoundException;

class QaTaskStatusRepository
{
    public function find(int $id): QaTaskStatus
    {
        if ($status = QaTaskStatus::findOne($id)) {
            return $status;
        }
        throw new NotFoundException('Qa Task Status is not found', QaTaskCodeException::QA_TASK_STATUS_NOT_FOUND);
    }

    public function save(QaTaskStatus $status): int
    {
        if (!$status->save(false)) {
            throw new \RuntimeException('Saving error', QaTaskCodeException::QA_TASK_STATUS_SAVE);
        }
        return $status->ts_id;
    }

    public function remove(QaTaskStatus $status): void
    {
        if (!$status->delete()) {
            throw new \RuntimeException('Removing error', QaTaskCodeException::QA_TASK_STATUS_REMOVE);
        }
    }
}

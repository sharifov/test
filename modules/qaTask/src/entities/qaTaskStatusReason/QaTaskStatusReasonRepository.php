<?php

namespace modules\qaTask\src\entities\qaTaskStatusReason;

use modules\qaTask\src\exceptions\QaTaskCodeException;
use sales\repositories\NotFoundException;

class QaTaskStatusReasonRepository
{
    public function find(int $id): QaTaskStatusReason
    {
        if ($reason = QaTaskStatusReason::findOne($id)) {
            return $reason;
        }
        throw new NotFoundException('Qa Task Status Reason is not found', QaTaskCodeException::QA_TASK_STATUS_REASON_NOT_FOUND);
    }

    public function save(QaTaskStatusReason $reason): int
    {
        if (!$reason->save(false)) {
            throw new \RuntimeException('Saving error', QaTaskCodeException::QA_TASK_STATUS_REASON_SAVE);
        }
        return $reason->tsr_id;
    }

    public function remove(QaTaskStatusReason $reason): void
    {
        if (!$reason->delete()) {
            throw new \RuntimeException('Removing error', QaTaskCodeException::QA_TASK_STATUS_REASON_REMOVE);
        }
    }
}

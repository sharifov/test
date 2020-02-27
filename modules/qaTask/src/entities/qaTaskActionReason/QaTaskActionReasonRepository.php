<?php

namespace modules\qaTask\src\entities\qaTaskActionReason;

use modules\qaTask\src\exceptions\QaTaskCodeException;
use sales\repositories\NotFoundException;

class QaTaskActionReasonRepository
{
    public function find(int $id): QaTaskActionReason
    {
        if ($reason = QaTaskActionReason::findOne($id)) {
            return $reason;
        }
        throw new NotFoundException('Qa Task Action Reason is not found', QaTaskCodeException::QA_TASK_ACTION_REASON_NOT_FOUND);
    }

    public function save(QaTaskActionReason $reason): int
    {
        if (!$reason->save(false)) {
            throw new \RuntimeException('Saving error', QaTaskCodeException::QA_TASK_ACTION_REASON_SAVE);
        }
        return $reason->tar_id;
    }

    public function remove(QaTaskActionReason $reason): void
    {
        if (!$reason->delete()) {
            throw new \RuntimeException('Removing error', QaTaskCodeException::QA_TASK_ACTION_REASON_REMOVE);
        }
    }
}

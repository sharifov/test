<?php

namespace src\model\leadStatusReasonLog;

use src\model\leadStatusReasonLog\entity\LeadStatusReasonLog;

class LeadStatusReasonLogRepository
{
    public function save(LeadStatusReasonLog $log): int
    {
        if (!$log->save()) {
            throw new \RuntimeException('Lead Status Reason Log saving failed: ' . $log->getErrorSummary(true)[0]);
        }
        return $log->lsrl_id;
    }
}

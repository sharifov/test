<?php

namespace src\model\leadStatusReason;

use src\model\leadStatusReason\entity\LeadStatusReason;

class LeadStatusReasonRepository
{
    public function save(LeadStatusReason $leadStatusReason): int
    {
        if (!$leadStatusReason->save()) {
            throw new \RuntimeException('LeadStatusReason saving failed: ' . $leadStatusReason->getErrorSummary(true)[0]);
        }
        return $leadStatusReason->lsr_id;
    }
}

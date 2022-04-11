<?php

namespace src\listeners\cases;

use src\entities\cases\CasesStatus;
use src\entities\cases\events\CasesBookingIdChangeEvent;
use src\entities\cases\CaseEventLog;
use Yii;

class CasesBookingIdChangeEventLogListener
{
    public function handle(CasesBookingIdChangeEvent $event): void
    {
        try {
            $description = 'Case BookingId changed to: ' . $event->case->cs_order_uid . 'by: ' . $event->username;
            $event->case->addEventLog(CaseEventLog::CASE_BOOKINGID_CHANGE, $description);
        } catch (\Throwable $e) {
            Yii::error(['message' => 'Case BookingId Change Event Log error', 'e' => $e->getMessage(), 'caseId' => $event->case->cs_id], 'Listeners:CasesBookingIdChangeEventLogListener');
        }
    }
}

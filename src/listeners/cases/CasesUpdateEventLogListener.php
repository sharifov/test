<?php

namespace src\listeners\cases;

use src\entities\cases\CasesStatus;
use src\entities\cases\events\CasesUpdatedEvent;
use src\entities\cases\CaseEventLog;
use Yii;

class CasesUpdateEventLogListener
{
    public function handle(CasesUpdatedEvent $event): void
    {
        try {
            $department = $event->case->cs_dep_id ? ', department: ' . $event->case->department->dep_name : '';
            $category = $event->case->cs_category_id ? ', category: ' . $event->case->category->cc_name : '';
            $description = 'Case updated by: ' . $event->username . $department . $category . ', subject: ' . $event->case->cs_subject . ', Booking ID: ' . $event->case->cs_order_uid;
            $data = [
                'department_key' => $event->case->cs_dep_id,
                'category_key' => $event->case->cs_category_id,
                'subject' => $event->case->cs_subject,
                'description' => $event->case->cs_description,
                'case_order_uid' => $event->case->cs_order_uid,
                'user_id' => $event->case->cs_user_id,
            ];
            $event->case->addEventLog(CaseEventLog::CASE_INFO_UPDATE, $description, $data);
        } catch (\Throwable $e) {
            Yii::error(['message' => 'Case Update Event Log error', 'e' => $e->getMessage(), 'caseId' => $event->case->cs_id], 'Listeners:CasesUpdateEventLogListener');
        }
    }
}

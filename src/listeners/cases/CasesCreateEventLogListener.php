<?php

namespace src\listeners\cases;

use src\entities\cases\CasesStatus;
use src\entities\cases\events\CasesCreatedEvent;
use src\entities\cases\CaseEventLog;
use Yii;

class CasesCreateEventLogListener
{
    public function handle(CasesCreatedEvent $event): void
    {
        try {
            $category = $event->case->cs_category_id ? ', category: ' . $event->case->category->cc_name : '';
            $department = $event->case->cs_dep_id ? 'department: ' . $event->case->department->dep_name : '';
            $description = CasesStatus::STATUS_LIST[$event->case->cs_status] . ' Case created for ' . $department . $category . ', subject:' . $event->case->cs_subject;
            $data = [
                'status_id' => $event->case->cs_status,
                'department_key' => $event->case->cs_dep_id,
                'category_key' => $event->case->cs_category_id,
                'subject' => $event->case->cs_subject,
                'description' => $event->case->cs_description,
                'case_order_uid' => $event->case->cs_order_uid,
                'user_id' => $event->case->cs_user_id,
            ];
            $event->case->addEventLog(CaseEventLog::CASE_CREATED, $description, $data);
        } catch (\Throwable $e) {
            Yii::error(['message' => 'Case Create Event Log error', 'e' => $e->getMessage(), 'caseId' => $event->case->cs_id], 'Listeners:CasesCreateEventLogListener');
        }
    }
}

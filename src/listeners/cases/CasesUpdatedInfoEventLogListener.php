<?php

namespace src\listeners\cases;

use src\entities\cases\CasesStatus;
use src\entities\cases\events\CasesUpdatedInfoEvent;
use src\entities\cases\CaseEventLog;
use common\models\Employee;
use Yii;

class CasesUpdatedInfoEventLogListener
{
    public function handle(CasesUpdatedInfoEvent $event): void
    {
        try {
            $department = $event->case->cs_dep_id ? ', department: ' . $event->case->department->dep_name : '';
            $category = $event->case->cs_category_id ? ', category: ' . $event->case->category->cc_name : '';
            $username = $event->userId ? Employee::findOne($event->userId)->username : 'System';
            $description = 'Case updated by: ' . $username . $department . $category . ', subject: ' . $event->case->cs_subject . ', Booking ID: ' . $event->case->cs_order_uid;
            $data = [
                'department_key' => $event->case->cs_dep_id,
                'category_key' => $event->case->cs_category_id,
                'subject' => $event->case->cs_subject,
                'description' => $event->case->cs_description,
                'case_order_uid' => $event->case->cs_order_uid,
                'user_id' => $event->case->cs_user_id,
            ];
            $event->case->addEventLog(CaseEventLog::CASE_UPDATE_INFO, $description, $data);
        } catch (\Throwable $e) {
            Yii::error(['message' => 'Case Update Event Log error', 'e' => $e->getMessage(), 'caseId' => $event->case->cs_id], 'Listeners:CasesUpdateEventLogListener');
        }
    }
}

<?php

namespace modules\taskList\src\notifications;

use common\models\Lead;
use common\models\Notifications;

/**
 * @property Lead $lead
 */
class LeadTasksListSavedNotification
{
    protected Lead $lead;

    /**
     * @param Lead $lead
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    /**
     * @param bool $isSuccessSaved
     * @return bool
     */
    public function send(bool $isSuccessSaved = true): bool
    {
        return Notifications::pub(['lead-' . $this->lead->id], 'refreshTaskList', [
            'data' => [
                'gid' => $this->lead->gid,
                'leadId' => $this->lead->id,
                'isSuccessSaved' => $isSuccessSaved,
            ],
        ]);
    }
}

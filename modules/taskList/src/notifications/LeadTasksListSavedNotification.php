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
    public function handle(bool $isSuccessSaved = true): bool
    {
        $isSuccess = Notifications::pub(['lead-' . $this->lead->id], 'leadTasksListSaved', [
            'data' => [
                'gid' => $this->lead->gid,
                'leadId' => $this->lead->id,
                'isSuccessSaved' => $isSuccessSaved,
            ],
        ]);

        return $isSuccess;
    }
}

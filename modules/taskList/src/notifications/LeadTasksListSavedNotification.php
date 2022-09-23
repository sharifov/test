<?php

namespace modules\taskList\src\notifications;

use common\models\Lead;
use common\models\Notifications;
use modules\featureFlag\FFlag;

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
     * @return bool
     */
    public function send(): bool
    {
        $result = false;
        /** @fflag FFlag::FF_KEY_AUTO_REFRESH_LEAD_TASK_LIST_ENABLE, Auto refresh lead task list enabled */
        if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_AUTO_REFRESH_LEAD_TASK_LIST_ENABLE)) {
            sleep(2);
            $result = Notifications::pub(['lead-' . $this->lead->id], 'refreshTaskList', [
                'data' => [
                    'gid' => $this->lead->gid,
                    'leadId' => $this->lead->id,
                    'isSavedAction' => true,
                ],
            ]);
        }

        return $result;
    }
}

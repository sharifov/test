<?php

namespace modules\lead\src\notifications\Task;

use common\models\Lead;
use common\models\Notifications;
use modules\featureFlag\FFlag;
use modules\taskList\src\entities\TaskListNotificationInterface;

/**
 * @property Lead $lead
 */
abstract class AbstractLeadTaskListListNotification implements TaskListNotificationInterface
{
    /** @var Lead  */
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

        if ($this->isEnabledAutoRefreshFF()) {
            sleep(2);
            $result = Notifications::pub(['lead-' . $this->lead->id], 'refreshTaskList', [
                'data' => [
                    'gid' => $this->lead->gid,
                    'leadId' => $this->lead->id,
                    'notificationActionType' => static::NOTIFY_TYPE,
                ],
            ]);
        }

        return $result;
    }

    /**
     * @return bool
     */
    protected function isEnabledAutoRefreshFF(): bool
    {
        /** @fflag FFlag::FF_KEY_AUTO_REFRESH_LEAD_TASK_LIST_ENABLE, Auto refresh lead task list enabled */
        return \Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_AUTO_REFRESH_LEAD_TASK_LIST_ENABLE);
    }
}

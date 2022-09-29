<?php

namespace modules\taskList\src\notifications\Task;

use common\models\Lead;
use common\models\Notifications;
use frontend\widgets\userTasksList\helpers\UserTasksListHelper;
use modules\featureFlag\FFlag;
use modules\taskList\src\entities\TargetObjectNotificationTypes;
use modules\taskList\src\entities\TaskListNotificationInterface;
use yii\caching\TagDependency;

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
     * @return self
     */
    public function send(): self
    {
        if ($this->isEnabledSend()) {
            sleep(2);
            $isSuccess = Notifications::pub(['lead-' . $this->lead->id], 'refreshTaskList', [
                'data' => [
                    'gid' => $this->lead->gid,
                    'leadId' => $this->lead->id,
                    'notificationActionType' => static::getType(),
                ],
            ]);

            if ($isSuccess && static::getType() === TargetObjectNotificationTypes::COMPLETE_TYPE) {
                /** @fflag FFlag::FF_KEY_USER_NEW_TASK_LIST_CACHE_DURATION, Cache duration for new user task list (in seconds) */
                if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_USER_NEW_TASK_LIST_CACHE_DURATION)) {
                    TagDependency::invalidate(\Yii::$app->cache, UserTasksListHelper::getCacheTagKey($this->lead->id, $this->lead->employee_id));
                }
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public static function getType(): string
    {
        return static::NOTIFY_TYPE;
    }

    /**
     * @return bool
     */
    protected function isEnabledSend(): bool
    {
        /** @fflag FFlag::FF_KEY_AUTO_REFRESH_LEAD_TASK_LIST_ENABLE, Auto refresh lead task list enabled */
        return \Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_AUTO_REFRESH_LEAD_TASK_LIST_ENABLE);
    }
}

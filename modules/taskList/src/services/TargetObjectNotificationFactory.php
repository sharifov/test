<?php

namespace modules\taskList\src\services;

use modules\taskList\src\entities\TargetObject;
use modules\lead\src\notifications\Task\LeadTasksListCompleteNotification;
use modules\lead\src\notifications\Task\LeadTasksListSavedNotification;
use modules\lead\src\notifications\Task\AbstractLeadTaskListListNotification;
use modules\taskList\src\entities\TaskListNotificationInterface;

/**
 * @property string $targetObject
 * @property int $targetObjectId
 */
class TargetObjectNotificationFactory
{
    protected string $targetObject;
    protected int $targetObjectId;

    /**
     * @param string $targetObject
     * @param int $targetObjectId
     */
    public function __construct(string $targetObject, int $targetObjectId)
    {
        $this->targetObject = $targetObject;
        $this->targetObjectId = $targetObjectId;
    }

    /**
     * @param string $type
     * @return TaskListNotificationInterface
     */
    public function create(string $type): TaskListNotificationInterface
    {
        $targetObjectEntity = (new TargetObjectFactory($this->targetObject, $this->targetObjectId))
            ->create();

        switch ($this->targetObject) {
            case TargetObject::TARGET_OBJ_LEAD:
                if ($type == LeadTasksListCompleteNotification::NOTIFY_TYPE) {
                    return new LeadTasksListCompleteNotification($targetObjectEntity);
                } elseif ($type == LeadTasksListSavedNotification::NOTIFY_TYPE) {
                    return new LeadTasksListSavedNotification($targetObjectEntity);
                }

                throw new \RuntimeException('TargetObjectNotification (' . TargetObject::TARGET_OBJ_LEAD . ') with type "' . $type . '" not found');
            break;
        }

        throw new \RuntimeException('TargetObjectNotification (' . $this->targetObject . ') with type "' . $type . '" unprocessed');
    }
}

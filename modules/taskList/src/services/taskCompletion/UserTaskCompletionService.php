<?php

namespace modules\taskList\src\services\taskCompletion;

use modules\taskList\src\entities\taskList\TaskListQuery;
use modules\taskList\src\entities\userTask\repository\UserTaskRepository;
use modules\taskList\src\entities\userTask\UserTaskQuery;
use modules\taskList\src\services\taskCompletion\taskCompletionChecker\TaskListCompletionFactory;

class UserTaskCompletionService
{
    private string $targetObject;
    private int $targetObjectId;
    private string $taskObject;
    private int $taskModelId;
    private int $userId;

    private array $userTasksProcessed = [];

    public function __construct(
        string $targetObject,
        int $targetObjectId,
        string $taskObject,
        int $taskModelId,
        int $userId
    ) {
        $this->targetObject = $targetObject;
        $this->targetObjectId = $targetObjectId;
        $this->taskObject = $taskObject;
        $this->taskModelId = $taskModelId;
        $this->userId = $userId;
    }

    /**
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function handle(): UserTaskCompletionService
    {
        $taskListsQuery = TaskListQuery::getTaskListUserCompletion(
            $this->userId,
            $this->targetObject,
            $this->targetObjectId,
            $this->taskObject,
            TaskCompletionDictionary::getUserTaskProcessingStatuses()
        );
        $taskLists = $taskListsQuery->all();

        $taskModel = (new TaskObjectModelFinder($this->taskObject, $this->taskModelId))->findModel();

        if ($taskLists) {
            foreach ($taskLists as $taskList) {
                $completionChecker = (new TaskListCompletionFactory(
                    $this->taskObject,
                    $taskModel,
                    $taskList
                ))->create();

                if (!$completionChecker->check()) {
                    continue;
                }

                $userTaskQuery = UserTaskQuery::getUserTaskCompletion(
                    $taskList->tl_id,
                    $this->userId,
                    $this->targetObject,
                    $this->targetObjectId,
                    TaskCompletionDictionary::getUserTaskProcessingStatuses(),
                    $this->userTasksProcessed
                );

                if ($userTask = $userTaskQuery->limit(1)->one()) {
                    $userTask->setStatusComplete();
                    (new UserTaskRepository($userTask))->save();
                    $this->userTasksProcessed[] = $userTask->ut_id;
                }
            }
        }
        return $this;
    }

    public function getUserTasksProcessed(): array
    {
        return $this->userTasksProcessed;
    }
}

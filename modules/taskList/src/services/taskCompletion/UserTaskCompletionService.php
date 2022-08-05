<?php

namespace modules\taskList\src\services\taskCompletion;

use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
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
        $this->log('Begin handle', '1');

        $taskListsQuery = TaskListQuery::getTaskListUserCompletion(
            $this->userId,
            $this->targetObject,
            $this->targetObjectId,
            $this->taskObject,
            TaskCompletionDictionary::getUserTaskProcessingStatuses()
        );
        $taskLists = $taskListsQuery->all();

        $this->log('Search taskLists result', '2', ['count' => count($taskLists)]);

        $taskModel = (new TaskObjectModelFinder($this->taskObject, $this->taskModelId))->findModel();

        if ($taskLists) {
            foreach ($taskLists as $taskList) {
                $this->log('TaskList begin processing', '3', ['taskListId' => $taskList->tl_id]);
                $userTaskQuery = UserTaskQuery::getUserTaskCompletion(
                    $taskList->tl_id,
                    $this->userId,
                    $this->targetObject,
                    $this->targetObjectId,
                    TaskCompletionDictionary::getUserTaskProcessingStatuses(),
                    UserShiftSchedule::getProcessingStatuses(),
                    (new \DateTimeImmutable('now', new \DateTimeZone('UTC'))),
                    $this->userTasksProcessed
                );

                if (!$userTask = $userTaskQuery->limit(1)->one()) {
                    continue;
                }

                $this->log('UserTask found', '4', ['userTaskId' => $userTask->ut_id]);

                $completionChecker = (new TaskListCompletionFactory(
                    $this->taskObject,
                    $taskModel,
                    $taskList,
                    $userTask
                ))->create();

                $isCheck = $completionChecker->check();

                $this->log('CompletionChecker', '5', ['isCheck' => $isCheck]);

                if (!$isCheck) {
                    continue;
                }

                $userTask->setStatusComplete();
                (new UserTaskRepository($userTask))->save();

                $this->log('UserTask set to completed', '6', ['userTaskId' => $userTask->ut_id]);

                $this->userTasksProcessed[] = $userTask->ut_id;
            }
        }
        return $this;
    }

    public function getUserTasksProcessed(): array
    {
        return $this->userTasksProcessed;
    }

    private function log(string $message, string $point = '', ?array $additionalData = null): void
    {
        /** @fflag FFlag::FF_KEY_USER_TASK_COMPLETION_DEBUG, Enable debug/log mode */
        if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_USER_TASK_COMPLETION_DEBUG)) {
            $logData['message'] = $message;
            $logData['targetObject'] = $this->targetObject;
            $logData['targetObjectId'] = $this->targetObjectId;
            $logData['taskObject'] = $this->taskObject;
            $logData['taskModelId'] = $this->taskModelId;
            $logData['userId'] = $this->userId;

            if ($additionalData) {
                $logData = array_merge($logData, $additionalData);
            }
            \Yii::info($logData, 'info\UserTaskCompletionService:point:' . $point);
        }
    }
}

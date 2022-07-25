<?php

namespace common\components\jobs;

use modules\taskList\src\services\taskCompletion\UserTaskCompletionService;
use src\helpers\app\AppHelper;
use yii\queue\JobInterface;

/**
 * Class UserTaskCompletionJob
 */
class UserTaskCompletionJob extends BaseJob implements JobInterface
{
    private string $targetObject;
    private int $targetObjectId;
    private string $taskObject;
    private int $taskModelId;
    private int $userId;

    public function __construct(
        string $targetObject,
        int $targetObjectId,
        string $taskObject,
        int $taskModelId,
        int $userId,
        ?float $timeStart = null,
        array $config = []
    ) {
        $this->targetObject = $targetObject;
        $this->targetObjectId = $targetObjectId;
        $this->taskObject = $taskObject;
        $this->taskModelId = $taskModelId;
        $this->userId = $userId;

        parent::__construct($timeStart, $config);
    }

    /**
     * @param $queue
     */
    public function execute($queue): void
    {
        $this->waitingTimeRegister();

        try {
            $userTaskCompletionService = new UserTaskCompletionService(
                $this->targetObject,
                $this->targetObjectId,
                $this->taskObject,
                $this->taskModelId,
                $this->userId
            );
            $userTaskCompletionService->handle();
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['logData'] = $this->logData();
            \Yii::warning($message, 'UserTaskCompletionJob:execute:Exception');
        } catch (\Throwable $throwable) {
            $message = AppHelper::throwableLog($throwable);
            $message['logData'] = $this->logData();
            \Yii::error($message, 'UserTaskCompletionJob:execute:Throwable');
        }
    }

    private function logData(): array
    {
        return [
            'targetObject' => $this->targetObject,
            'targetObjectId' => $this->targetObjectId,
            'taskObject' => $this->taskObject,
            'taskModelId' => $this->taskModelId,
            'userId' => $this->userId,
        ];
    }
}

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
        $timeStart = microtime(true);
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

        $timeEnd = microtime(true);
        $executeSecond = (int)($timeEnd - $timeStart);

        /** @fflag FFlag::FF_KEY_USER_TASK_COMPLETION_DEBUG, Enable debug/log mode */
        if (\Yii::$app->featureFlag->isEnable(\modules\featureFlag\FFlag::FF_KEY_USER_TASK_COMPLETION_DEBUG)) {
            $message = [
                'message' => 'Debug execute time',
                'executeSecond' => $executeSecond,
                'logData' => $this->logData(),
            ];
            if ($executeSecond > 5) {
                \Yii::warning($message, 'UserTaskCompletionJob:execute:warning');
            } else {
                \Yii::info($message, 'info\UserTaskCompletionJob:execute:executeSecond');
            }
        }

        $this->execTimeRegister();
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

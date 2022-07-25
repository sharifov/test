<?php

namespace modules\taskList\src\services\taskCompletion;

use modules\taskList\src\entities\TaskObject;
use ReflectionClass;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class TaskObjectModelFinder
{
    private string $taskObject;
    private int $taskModelId;

    public function __construct(
        string $taskObject,
        int $taskModelId
    ) {
        $this->taskObject = $taskObject;
        $this->taskModelId = $taskModelId;
    }

    public function findModel()
    {
        if (ArrayHelper::keyExists($this->taskObject, TaskObject::OBJ_TASK_LIST)) {
            /** @var ActiveRecord $taskObjectClass */
            $taskObjectClass = TaskObject::OBJ_TASK_LIST[$this->taskObject];
            if (!$model = $taskObjectClass::findOne($this->taskModelId)) {
                throw new \RuntimeException((new ReflectionClass($taskObjectClass))->getShortName() . ' not found by ID(' . $this->taskModelId . ')');
            }
            return $model;
        }

        throw new \RuntimeException('TaskObject (' . $this->taskObject . ') unprocessed');
    }
}

<?php

namespace modules\taskList\src\services\taskCompletion;

use common\models\Sms;
use modules\taskList\src\entities\TaskObject;
use src\repositories\email\EmailRepositoryFactory;

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
        switch ($this->taskObject) {
            case TaskObject::OBJ_EMAIL:
                try {
                    $model = EmailRepositoryFactory::getRepository()->find($this->taskModelId);
                } catch (\Throwable $e) {
                    throw new \RuntimeException($e->getMessage());
                }
                return $model;
            case TaskObject::OBJ_SMS:
                if (!$model = Sms::find()->where(['s_id' => $this->taskModelId])->limit(1)->one()) {
                    throw new \RuntimeException('Sms not found by ID(' . $this->taskModelId . ')');
                }
                return $model;
        }
        throw new \RuntimeException('TaskObject (' . $this->taskObject . ') unprocessed');
    }
}

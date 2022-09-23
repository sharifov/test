<?php

namespace modules\taskList\src\services;

use common\models\Lead;
use modules\taskList\src\entities\TargetObject;
use yii\base\Model;

class TargetObjectFactory
{
    private string $targetObject;
    private int $targetObjectId;

    public function __construct(string $targetObject, int $targetObjectId)
    {
        $this->targetObject = $targetObject;
        $this->targetObjectId = $targetObjectId;
    }

    public function create(): Model
    {
        switch ($this->targetObject) {
            case TargetObject::TARGET_OBJ_LEAD:
                if (!$lead = Lead::find()->where(['id' => $this->targetObjectId])->limit(1)->one()) {
                    throw new \RuntimeException('targetObject (' . $this->targetObject . ') not found');
                }
                return $lead;

            /* TODO: Case  */
        }
        throw new \RuntimeException('targetObject (' . $this->targetObject . ') unprocessed');
    }
}

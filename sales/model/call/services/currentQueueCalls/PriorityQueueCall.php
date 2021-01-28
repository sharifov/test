<?php

namespace sales\model\call\services\currentQueueCalls;

use yii\base\Model;

class PriorityQueueCall extends Model
{
    public $count;
    public $project;
    public $department;

    public function getData(): array
    {
        return $this->getAttributes();
    }
}

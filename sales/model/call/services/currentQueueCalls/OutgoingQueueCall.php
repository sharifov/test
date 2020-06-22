<?php

namespace sales\model\call\services\currentQueueCalls;

use yii\base\Model;
use yii\helpers\Json;

class OutgoingQueueCall extends Model
{
    public $callId;
    public $type;
    public $status;
    public $duration;
    public $projectName;
    public $sourceName;
    public $phone;
    public $name;

    public function getData(): array
    {
        $attributes = $this->getAttributes();
        $attributes['contact'] = [
            'name' => $this->name
        ];
        return $attributes;
    }

    public function toJson(): string
    {
        return Json::encode($this->getData());
    }
}

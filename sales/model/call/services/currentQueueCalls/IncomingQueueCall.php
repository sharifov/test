<?php

namespace sales\model\call\services\currentQueueCalls;

use yii\base\Model;
use yii\helpers\Json;

class IncomingQueueCall extends Model
{
    public $fromInternal;
    public $callId;
    public $callSid;
    public $type;
    public $name;
    public $projectName;
    public $sourceName;
    public $phone;

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

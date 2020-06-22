<?php

namespace sales\model\call\services\currentQueueCalls;

use yii\base\Model;
use yii\helpers\Json;

class ActiveQueueCall extends Model
{
    public $callId;
    public $isMute;
    public $isListen;
    public $isHold;
    public $typeId;
    public $type;
    public $phone;
    public $name;
    public $duration;
    public $projectName;
    public $sourceName;
    public $holdDuration;

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

<?php

namespace frontend\widgets\newWebPhone\call;

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

    public function toJson(): string
    {
        $attributes = $this->getAttributes();
        $attributes['contact'] = [
            'name' => $this->name
        ];
        return Json::encode($attributes);
    }
}

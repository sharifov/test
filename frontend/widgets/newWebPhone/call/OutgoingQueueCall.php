<?php

namespace frontend\widgets\newWebPhone\call;

use yii\base\BaseObject;
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

    public function toJson(): string
    {
        $attributes = $this->getAttributes();
        $attributes['contact'] = [
            'name' => $this->name
        ];
        return Json::encode($attributes);
    }
}

<?php

namespace frontend\widgets\newWebPhone\call;

use yii\base\BaseObject;
use yii\base\Model;
use yii\helpers\Json;

class IncomingQueueCall extends Model
{
    public $fromInternal;
    public $callId;
    public $type;
    public $name;
    public $projectName;
    public $sourceName;
    public $phone;

    public function toJson(): string
    {
        $attributes = $this->getAttributes();
        $attributes['contact'] = [
            'name' => $this->name
        ];
        return Json::encode($attributes);
    }
}

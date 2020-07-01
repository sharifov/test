<?php

namespace sales\model\call\services\currentQueueCalls;

use yii\base\Model;

class ActiveQueueCall extends Model
{
    public $callSid;
    public $status;
    public $leadId;
    public $source_type_id;
    public $fromInternal;
    public $isMute;
    public $isListen;
    public $isHold;
    public $holdDuration;
    public $typeId;
    public $type;
    public $phone;
    public $name;
    public $company;
    public $duration;
    public $project;
    public $source;
    public $department;
    public $queue = 'inProgress';
    public $isEnded = false;

    public function getData(): array
    {
        $attributes = $this->getAttributes();

        $attributes['contact'] = [
            'name' => $this->name,
            'phone' => $this->phone,
            'company' => $this->company
        ];

        unset($attributes['name'], $attributes['phone'], $attributes['company']);

        return $attributes;
    }
}

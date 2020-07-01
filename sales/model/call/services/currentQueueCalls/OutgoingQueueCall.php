<?php

namespace sales\model\call\services\currentQueueCalls;

use yii\base\Model;

class OutgoingQueueCall extends Model
{
    public $callSid;
    public $type;
    public $status;
    public $duration;
    public $project;
    public $source;
    public $name;
    public $phone;
    public $company;
    public $department;

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

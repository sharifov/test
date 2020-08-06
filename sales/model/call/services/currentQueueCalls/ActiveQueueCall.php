<?php

namespace sales\model\call\services\currentQueueCalls;

use common\models\Call;
use yii\base\Model;

class ActiveQueueCall extends Model
{
    public $callSid;
    public $conferenceSid;
    public $status;
    public $duration;
    public $leadId;
    public $typeId;
    public $type;
    public $source_type_id;
    public $fromInternal;
    public $isInternal;
    public $isHold;
    public $holdDuration;
    public $isListen;
    public $isMute;
    public $isCoach;
    public $isBarge;
    public $project;
    public $source;
    public $isEnded = false;
    public $phone;
    public $name;
    public $company;
    public $department;
    public $queue = Call::QUEUE_IN_PROGRESS;
    public $isConferenceCreator;

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

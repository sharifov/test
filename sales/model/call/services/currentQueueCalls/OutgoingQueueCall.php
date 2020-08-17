<?php

namespace sales\model\call\services\currentQueueCalls;

use yii\base\Model;

class OutgoingQueueCall extends Model
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
    public $queue;
    public $canContactDetails;
    public $canCallInfo;
    public $isClient;
    public $clientId;

    public function getData(): array
    {
        $attributes = $this->getAttributes();

        $attributes['contact'] = [
            'id' => $this->clientId,
            'name' => $this->name,
            'phone' => $this->phone,
            'company' => $this->company,
            'canContactDetails' => $this->canContactDetails,
            'canCallInfo' => $this->canCallInfo,
            'isClient' => $this->isClient,
            'callSid' => $this->callSid,
        ];

        unset($attributes['clientId'], $attributes['name'], $attributes['phone'], $attributes['company'], $attributes['canContactDetails'], $attributes['canCallInfo'], $attributes['isClient']);

        return $attributes;
    }
}

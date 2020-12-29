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
    public $isJoin;
    public $project;
    public $source;
    public $isEnded = false;
    public $phone;
    public $name;
    public $company;
    public $department;
    public $queue = Call::QUEUE_IN_PROGRESS;
    public $isConferenceCreator;
    public $canContactDetails;
    public $canCallInfo;
    public $isClient;
    public $clientId;
    public $recordingDisabled;

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

<?php

namespace sales\model\call\services\currentQueueCalls;

use common\models\Call;
use common\models\CallUserAccess;
use common\models\Department;
use sales\model\call\helper\CallHelper;
use yii\base\Model;

class IncomingQueueCall extends Model
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
    public $queue;
    public $canContactDetails;
    public $canCallInfo;
    public $isClient;
    public $clientId;
    public $recordingDisabled;
    public $isWarmTransfer;
    public $id;
    public $callAntiSpamData;

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

    public static function createIncoming(CallUserAccess $access, $canContactDetails, $canCallInfo, $callAntiSpamData): self
    {
        $call = $access->cuaCall;

        return new self([
            'callSid' => $call->c_call_sid,
            'conferenceSid' => $call->c_conference_sid,
            'status' => $call->getStatusName(),
            'duration' => time() - strtotime($call->c_updated_dt),
            'leadId' => $call->c_lead_id,
            'typeId' => $access->isWarmTransfer() ? Call::CALL_TYPE_IN : $call->c_call_type_id,
            'type' => $access->isWarmTransfer() ? 'Incoming' : CallHelper::getTypeDescription($call),
            'source_type_id' => $access->isWarmTransfer() ? Call::SOURCE_DIRECT_CALL : $call->c_source_type_id,
            'fromInternal' => false,
            'isInternal' => $call->isInternal(),
            'isHold' => false,
            'holdDuration' => 0,
            'isListen' => false,
            'isMute' => false,
            'isCoach' => false,
            'isBarge' => false,
            'isJoin' => $call->isJoin(),
            'project' => $call->c_project_id ? $call->cProject->name : '',
            'source' => $access->isWarmTransfer() ? Call::SOURCE_LIST[Call::SOURCE_DIRECT_CALL] : ($call->c_source_type_id ? $call->getSourceName() : ''),
            'phone' => $call->c_from,
            'name' => $call->cClient ? $call->cClient->getShortName() : '------',
            'company' => '',
            'department' => $call->c_dep_id ? Department::getName($call->c_dep_id) : '',
            'queue' => $access->isWarmTransfer() ? Call::QUEUE_DIRECT : Call::getQueueName($call),
            'canContactDetails' => $canContactDetails,
            'canCallInfo' => $canCallInfo,
            'isClient' => $call->c_client_id ? $call->cClient->isClient() : false,
            'clientId' => $call->c_client_id,
            'recordingDisabled' => $call->c_recording_disabled ? true : false,
            'isWarmTransfer' => $access->isWarmTransfer(),
            'id' => $call->c_id,
            'callAntiSpamData' => $callAntiSpamData
        ]);
    }

    public static function createHold(CallUserAccess $access, $canContactDetails, $canCallInfo, $callAntiSpamData): self
    {
        $call = $access->cuaCall;

        $phone = '';
        if ($call->isIn()) {
            $phone = $call->c_from;
        } elseif ($call->isOut()) {
            $phone = $call->c_to;
        }

        return new self([
            'callSid' => $call->c_call_sid,
            'conferenceSid' => $call->c_conference_sid,
            'status' => $call->getStatusName(),
            'duration' => time() - strtotime($call->c_updated_dt),
            'leadId' => $call->c_lead_id,
            'typeId' => $call->c_call_type_id,
            'type' => CallHelper::getTypeDescription($call),
            'source_type_id' => $call->c_source_type_id,
            'fromInternal' => false,
            'isInternal' => $call->isInternal(),
            'isHold' => false,
            'holdDuration' => 0,
            'isListen' => false,
            'isMute' => false,
            'isCoach' => false,
            'isBarge' => false,
            'isJoin' => $call->isJoin(),
            'project' => $call->c_project_id ? $call->cProject->name : '',
            'source' => $call->c_source_type_id ? $call->getSourceName() : '',
            'phone' => $phone,
            'name' => $call->cClient ? $call->cClient->getShortName() : '------',
            'company' => '',
            'department' => $call->c_dep_id ? Department::getName($call->c_dep_id) : '',
            'queue' => Call::getQueueName($call),
            'canContactDetails' => $canContactDetails,
            'canCallInfo' => $canCallInfo,
            'isClient' => $call->c_client_id ? $call->cClient->isClient() : false,
            'clientId' => $call->c_client_id,
            'recordingDisabled' => $call->c_recording_disabled ? true : false,
            'id' => $call->c_id,
            'callAntiSpamData' => $callAntiSpamData
        ]);
    }
}

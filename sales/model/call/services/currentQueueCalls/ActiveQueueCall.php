<?php

namespace sales\model\call\services\currentQueueCalls;

use common\models\Call;
use common\models\Conference;
use common\models\Department;
use common\models\Employee;
use sales\model\call\helper\CallHelper;
use sales\model\phoneList\entity\PhoneList;
use sales\model\voip\phoneDevice\device\VoipDevice;
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

    public static function create(Call $call, $userId, $canContactDetails, $canCallInfo, $callAntiSpamData): self
    {
        if ($call->isIn() || $call->isOut() || $call->isReturn()) {
            $name = $call->cClient ? $call->cClient->getShortName() : '------';
        } elseif ($call->isJoin() && ($parentJoin = $call->cParent) && $parentJoin->cCreatedUser) {
            $name = $parentJoin->cCreatedUser->nickname;
        } else {
            $name = '------';
        }

        $phone = '';
        if ($call->isIn()) {
            $phone = $call->c_from;
        } elseif ($call->isOut()) {
            if ($call->cParent && $call->currentParticipant && $call->currentParticipant->isAgent()) {
                $phone = $call->c_from;
            } else {
                $phone = $call->c_to;
            }
        } elseif ($call->isJoin() && ($parentJoin = $call->cParent)) {
            if ($parentJoin->isIn()) {
                $phone = $parentJoin->c_to;
            } elseif ($parentJoin->isOut()) {
                if (isset($parentJoin->cParent)) {
                    $phone = $parentJoin->cParent->c_from;
                } else {
                    $phone = $parentJoin->c_from;
                }
            }
        } elseif ($call->isReturn() && ($parentReturn = $call->cParent)) {
            if ($parentReturn->isIn()) {
                $phone = $parentReturn->c_from;
            } elseif ($parentReturn->isOut()) {
                $phone = $parentReturn->c_to;
            }
        }

        if ($call->currentParticipant && $call->currentParticipant->isUser()) {
            if ($call->isIn()) {
                if (($fromUserId = VoipDevice::getUserId($call->c_from)) && $fromUser = Employee::findOne($fromUserId)) {
                    $name = $fromUser->nickname ?: $fromUser->username;
                    $phone = '';
                }
            } elseif ($call->isOut()) {
                if (($toUserId = VoipDevice::getUserId($call->c_to)) && $toUser = Employee::findOne($toUserId)) {
                    $name = $toUser->nickname ?: $toUser->username;
                    $phone = '';
                }
            }
        }

        $isMute = false;
        $isListen = false;
        if ($call->currentParticipant && $call->currentParticipant->isMute()) {
            $isMute = true;
        }
        if ($call->isJoin() && $call->c_source_type_id === Call::SOURCE_LISTEN) {
            $isMute = true;
            $isListen = true;
        }
        $isCoach = false;
        if ($call->isJoin() && $call->c_source_type_id === Call::SOURCE_COACH) {
            $isCoach = true;
        }
        $isBarge = false;
        if ($call->isJoin() && $call->c_source_type_id === Call::SOURCE_BARGE) {
            $isBarge = true;
        }
        $isHold = false;
        $holdDuration = 0;
        if ($call->currentParticipant && $call->currentParticipant->isHold()) {
            $isHold = true;
            $holdDuration = time() - strtotime($call->currentParticipant->cp_hold_dt);
        }

        $isInternal = PhoneList::find()->byPhone($call->c_from)->enabled()->exists();

        if ($call->isJoin()) {
            $source = $call->c_parent_call_sid ? $call->cParent->getSourceName() : '';
        } else {
            $source = $call->getSourceName();
        }
        if ($source === '-') {
            $source = '';
        }

        //todo remove after removed not conference call
        $call->c_status_id = Call::STATUS_IN_PROGRESS;

        return new self([
            'callSid' => $call->c_call_sid,
            'conferenceSid' => $call->c_conference_sid,
            'status' => $call->getStatusName(),
            'duration' => time() - strtotime($call->c_updated_dt),
            'leadId' => $call->c_lead_id,
            'typeId' => $call->c_call_type_id,
            'type' => CallHelper::getTypeDescription($call),
            'source_type_id' => $call->c_source_type_id,
            'fromInternal' => $isInternal,
            'isInternal' => $call->isInternal(),
            'isHold' => $isHold,
            'holdDuration' => $holdDuration,
            'isListen' => $isListen,
            'isMute' => $isMute,
            'isCoach' => $isCoach,
            'isBarge' => $isBarge,
            'isJoin' => $call->isJoin(),
            'project' => $call->c_project_id ? $call->cProject->name : '',
            'source' => $source,
            'name' => $name,
            'phone' => $phone,
            'company' => '',
            'department' => $call->c_dep_id ? Department::getName($call->c_dep_id) : '',
            'isConferenceCreator' => Conference::find()->andWhere(['cf_id' => $call->c_conference_id, 'cf_status_id' => Conference::STATUS_START, 'cf_created_user_id' => $userId])->exists(),
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

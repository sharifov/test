<?php

namespace sales\model\call\socket;

use common\models\Call;
use common\models\Department;
use common\models\Employee;
use sales\helpers\UserCallIdentity;
use sales\model\call\helper\CallHelper;
use sales\model\call\services\currentQueueCalls\ActiveConference;
use sales\model\conference\service\ConferenceDataService;
use sales\model\phoneList\entity\PhoneList;

class CallUpdateMessage
{
    public function create(Call $call, bool $isChangedStatus, int $userId): array
    {
        $conferenceBase = (bool)(\Yii::$app->params['settings']['voip_conference_base'] ?? false);
        $fromInternal = PhoneList::find()->byPhone($call->c_from)->enabled()->exists();
        $name = '';
        $phone = '';

        if ($call->isIn()) {
            $phone = $call->c_from;
        } elseif ($call->isOut()) {
            if ($call->cParent) {
                $phone = $call->cParent->c_to;
//                }elseif ($call->cParent && $call->currentParticipant && $call->currentParticipant->isClient()) {
//                    $phoneFrom = $call->c_to;
            } else {
                $phone = $call->c_to;
            }
        } elseif ($call->isReturn() && ($parentReturn = $call->cParent)) {
            if ($parentReturn->isIn()) {
                $phone = $parentReturn->c_from;
            } elseif ($parentReturn->isOut()) {
                $phone = $parentReturn->c_to;
            }
        }

        if ($call->isJoin()) {
            if ($call->cParent && $call->cParent->cCreatedUser) {
                $name = $call->cParent->cCreatedUser->nickname;
                if ($call->cParent->isIn()) {
                    $phone = $call->cParent->c_to;
                } elseif ($call->cParent->isOut()) {
                    if (isset($call->cParent->cParent)) {
                        $phone = $call->cParent->cParent->c_from;
                    } else {
                        $phone = $call->cParent->c_from;
                    }
                }
            }
        } else {
            $name = $fromInternal ? $call->getCallerName($call->isIn() ? $call->c_from : $call->c_to) : ($call->c_client_id ? $call->cClient->getShortName() : 'ClientName');
        }

        if ($call->isInternal() || ($call->currentParticipant && $call->currentParticipant->isUser())) {
            if ($call->isIn()) {
                if (($fromUserId = UserCallIdentity::parseUserId($call->c_from)) && $fromUser = Employee::findOne($fromUserId)) {
                    $name = $fromUser->nickname ?: $fromUser->username;
                    $phone = '';
                }
            } elseif ($call->isOut()) {
                if (($toUserId = UserCallIdentity::parseUserId($call->c_to)) && $toUser = Employee::findOne($toUserId)) {
                    $name = $toUser->nickname ?: $toUser->username;
                    $phone = '';
                }
            }
        }

        $isHold = false;
        $isListen = false;
        $isMute = false;
        $holdDuration = 0;

        if ($call->currentParticipant && $call->currentParticipant->isHold()) {
            $isHold = true;
            $holdDuration = time() - strtotime($call->currentParticipant->cp_hold_dt);
        }

        if ($call->currentParticipant && $call->currentParticipant->isMute()) {
            $isMute = true;
        }

        if ($call->isJoin() && $call->c_source_type_id === Call::SOURCE_LISTEN) {
            $isListen = true;
            $isMute = true;
        }

        $isCoach = false;
        if ($call->isJoin() && $call->c_source_type_id === Call::SOURCE_COACH) {
            $isCoach = true;
        }

        $isBarge = false;
        if ($call->isJoin() && $call->c_source_type_id === Call::SOURCE_BARGE) {
            $isBarge = true;
        }

        $callSid = $call->c_call_sid;
        $callId = $call->c_id;

        if (!$conferenceBase) {
            if ($isChangedStatus && $call->isStatusInProgress() && $call->isOut() && $call->c_parent_id) {
                $callSid = $call->c_parent_call_sid ?: $call->cParent->c_call_sid;
                $callId = $call->c_parent_call_sid ?: $call->cParent->c_id;
            }
        }

        if ($call->isJoin()) {
            $source = $call->c_parent_call_sid ? $call->cParent->getSourceName() : '';
        } else {
            $source = $call->getSourceName();
        }
        if ($source === '-') {
            $source = '';
        }

        $conference = null;
        $isConferenceCreator = false;

        if ($call->c_conference_id && $call->isStatusInProgress() && $data = ConferenceDataService::getDataById($call->c_conference_id)) {
            $participants = [];
            foreach ($data['participants'] as $key => $part) {
                if (!$part['userId'] || $part['userId'] === $call->c_created_user_id) {
                    unset($part['userId']);
                    $participants[] = $part;
                }
            }

            $conference = new ActiveConference([
                'sid' => $data['conference']['sid'],
                'duration' => $data['conference']['duration'],
                'participants' => $participants,
            ]);

            if ($call->c_created_user_id === $data['conference']['creator']) {
                $isConferenceCreator = true;
            }
        }

        $auth = \Yii::$app->authManager;

        return [
            'id' => $callId,
            'callSid' => $callSid,
            'conferenceSid' => $call->c_conference_sid,
            'status' => $call->getStatusName(),
            'duration' => $call->c_call_duration,
            'snr' => $call->c_sequence_number,
            'leadId' => $call->c_lead_id,
            'typeId' => $call->c_call_type_id,
            'type' => CallHelper::getTypeDescription($call),
            'source_type_id' => $call->c_source_type_id,
            'fromInternal' => $fromInternal,
            'isInternal' => $call->isInternal(),
            'isHold' => $isHold,
            'holdDuration' => $holdDuration,
            'isListen' => $isListen,
            'isCoach' => $isCoach,
            'isMute' => $isMute,
            'isBarge' => $isBarge,
            'project' => $call->c_project_id ? $call->cProject->name : '',
            'source' => $source,
            'isEnded' => $call->isEnded(),
            'contact' => [
                'id' => $call->c_client_id,
                'name' => $name,
                'phone' => $phone,
                'company' => '',
                'isClient' => $call->c_client_id ? $call->cClient->isClient() : false,
                'canContactDetails' => $auth->checkAccess($userId, '/client/ajax-get-info'),
                'canCallInfo' => $auth->checkAccess($userId, '/call/ajax-call-info'),
                'callSid' => $callSid,
            ],
            'department' => $call->c_dep_id ? Department::getName($call->c_dep_id) : '',
            'queue' => Call::getQueueName($call),
            'conference' => $conference !== null ? $conference->getData() : null,
            'isConferenceCreator' => $isConferenceCreator,
        ];
    }
}

<?php

namespace sales\model\call\socket;

use common\components\i18n\Formatter;
use common\models\Call;
use common\models\Department;
use common\models\Employee;
use common\models\PhoneBlacklist;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use sales\guards\phone\PhoneBlackListGuard;
use sales\helpers\UserCallIdentity;
use sales\model\call\helper\CallHelper;
use sales\model\call\services\currentQueueCalls\ActiveConference;
use sales\model\client\query\ClientLeadCaseCounter;
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
                'recordingDisabled' => $data['conference']['recordingDisabled'],
            ]);

            if ($call->c_created_user_id === $data['conference']['creator']) {
                $isConferenceCreator = true;
            }
        }

        $auth = \Yii::$app->authManager;

        //$isPhoneInBlackList = PhoneBlacklist::find()->andWhere(['pbl_phone' => $phone, 'pbl_enabled' => true])->exists();
        $isPhoneInBlackList = PhoneBlacklist::find()->andWhere(['pbl_phone' => $phone])->andWhere('pbl_expiration_date > now()')->exists();
        //var_dump($isPhoneInBlackList); die();

        $callAntiSpam = [];
        $callAntiSpamData = [];
        if ($call->cParent && $call->cParent->c_data_json) {
            $callAntiSpam = json_decode($call->cParent->c_data_json, true)['callAntiSpamData'] ?? [];
        }
        if ($callAntiSpam) {
            $callAntiSpamData = [
                'type' => $callAntiSpam['type'] ?? null,
                'rate' => $callAntiSpam['rate'] ?? 0,
                'trustPercent' => $callAntiSpam['trustPercent'] ?? null
            ];
        }

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
            'isJoin' => $call->isJoin(),
            'project' => $call->c_project_id ? $call->cProject->name : '',
            'source' => $source,
            'isEnded' => $call->isEnded(),
            'contact' => [
                'id' => $call->c_client_id,
                'name' => $name,
                'phone' => $phone,
                'company' => '',
                'isClient' => $call->c_client_id && $call->cClient->isClient(),
                'canContactDetails' => $auth->checkAccess($userId, '/client/ajax-get-info'),
                'canCallInfo' => $auth->checkAccess($userId, '/call/ajax-call-info'),
                'callSid' => $callSid,
                'isPhoneInBlackList' => $isPhoneInBlackList
            ],
            'department' => $call->c_dep_id ? Department::getName($call->c_dep_id) : '',
            'queue' => Call::getQueueName($call),
            'conference' => $conference !== null ? $conference->getData() : null,
            'isConferenceCreator' => $isConferenceCreator,
            'recordingDisabled' => $call->c_recording_disabled,
            'blacklistBtnEnabled' => PhoneBlackListGuard::canAdd($userId),
            'callAntiSpamData' => $callAntiSpamData
        ];
    }

    public function getContactData(Call $call, int $userId): array
    {
        $conferenceBase = (bool)(\Yii::$app->params['settings']['voip_conference_base'] ?? false);
        $name = '';

        $callSid = $call->c_call_sid;
        if (!$conferenceBase) {
            if ($call->isStatusInProgress() && $call->isOut() && $call->c_parent_id) {
                $callSid = $call->c_parent_call_sid ?: $call->cParent->c_call_sid;
            }
        }

        if ($call->isJoin()) {
            if ($call->cParent && $call->cParent->cCreatedUser) {
                $name = $call->cParent->cCreatedUser->nickname;
            }
        } else {
            $fromInternal = PhoneList::find()->byPhone($call->c_from)->enabled()->exists();
            $name = $fromInternal ? $call->getCallerName($call->isIn() ? $call->c_from : $call->c_to) : ($call->c_client_id ? $call->cClient->getShortName() : 'ClientName');
        }

        if ($call->isInternal() || ($call->currentParticipant && $call->currentParticipant->isUser())) {
            if ($call->isIn()) {
                if (($fromUserId = UserCallIdentity::parseUserId($call->c_from)) && $fromUser = Employee::findOne($fromUserId)) {
                    $name = $fromUser->nickname ?: $fromUser->username;
                }
            } elseif ($call->isOut()) {
                if (($toUserId = UserCallIdentity::parseUserId($call->c_to)) && $toUser = Employee::findOne($toUserId)) {
                    $name = $toUser->nickname ?: $toUser->username;
                }
            }
        }

        $auth = \Yii::$app->authManager;

        $countActiveLeads = 0;
        $countAllLeads = 0;
        $canCreateLead = false;
        $clientLeads = [];
        $formatter = new Formatter();
        if ($call->c_client_id && $call->c_created_user_id) {
            $counter = new ClientLeadCaseCounter($call->c_client_id, $call->c_created_user_id);
            $countActiveLeads = $counter->countActiveLeads();
            $countAllLeads = $counter->countAllLeads();
            $leads = $call->cClient->leads;
            foreach ($leads as $lead) {
                $clientLeads[] = [
                    'status' => $lead->getStatusLabel($lead->status),
                    'formatHtml' => $formatter->asLead($lead),
                    'id' => $lead->id
                ];
            }
        }
        if ($call->c_created_user_id) {
            $leadAbacDto = new LeadAbacDto(null, $call->c_created_user_id);
            /** @abac new LeadAbacDto(null, $call->c_created_user_id), LeadAbacObject::ACT_CREATE_FROM_PHONE_WIDGET, LeadAbacObject::ACTION_CREATE, Restrict access to button create lead in phone widget in contact info block */
            $canCreateLead = (bool)\Yii::$app->abac->can($leadAbacDto, LeadAbacObject::ACT_CREATE_FROM_PHONE_WIDGET, LeadAbacObject::ACTION_CREATE, $call->cCreatedUser);
        }

        return [
            'id' => $call->c_client_id,
            'name' => $name,
            'company' => '',
            'callSid' => $callSid,
            'isClient' => $call->c_client_id && $call->cClient->isClient(),
            'canContactDetails' => $auth->checkAccess($userId, '/client/ajax-get-info'),
            'canCallInfo' => $auth->checkAccess($userId, '/call/ajax-call-info'),
            'countActiveLeads' => $countActiveLeads,
            'countAllLeads' => $countAllLeads,
            'canCreateLead' => $canCreateLead,
            'leads' => $clientLeads
        ];
    }
}

<?php

namespace sales\model\call\services\currentQueueCalls;

use common\models\Call;
use common\models\CallUserAccess;
use common\models\Conference;
use common\models\ConferenceParticipant;
use common\models\Department;
use common\models\Employee;
use common\models\Project;
use sales\helpers\UserCallIdentity;
use sales\model\call\helper\CallHelper;
use sales\model\conference\service\ConferenceDataService;
use sales\model\phoneList\entity\PhoneList;
use yii\db\Expression;

class CurrentQueueCallsService
{
    private int $userId;
    private bool $canContactDetails;
    private bool $canCallInfo;

    public function getQueuesCalls(int $userId, ?string $excludeCallSid, bool $generalLinePriorityIsEnabled): QueueCalls
    {
        $this->userId = $userId;

        $auth = \Yii::$app->authManager;
        $this->canContactDetails = $auth->checkAccess($this->userId, '/client/ajax-get-info');
        $this->canCallInfo = $auth->checkAccess($this->userId, '/call/ajax-call-info');

        $priorityQueue = $this->getPriorityCalls($generalLinePriorityIsEnabled);
        $holdQueue = $this->getHoldCalls($excludeCallSid);
        $incomingQueue = $this->getIncomingCalls($excludeCallSid, $generalLinePriorityIsEnabled);
        $outgoingQueue = $this->getOutgoingCalls($excludeCallSid);
        $activeQueue = $this->getActiveCalls($excludeCallSid);
        $conferences = $this->getActiveConferences($activeQueue['calls']);

        $queueCalls = new QueueCalls(
            $holdQueue,
            $incomingQueue['calls'],
            $outgoingQueue['calls'],
            $activeQueue['calls'],
            $conferences,
            $priorityQueue
        );

        if ($incomingQueue['last_time'] > $outgoingQueue['last_time'] && $incomingQueue['last_time'] > $activeQueue['last_time']) {
            $queueCalls->lastActiveQueue = QueueCalls::LAST_ACTIVE_INCOMING;
        } elseif ($outgoingQueue['last_time'] > $incomingQueue['last_time'] && $outgoingQueue['last_time'] > $activeQueue['last_time']) {
            $queueCalls->lastActiveQueue = QueueCalls::LAST_ACTIVE_OUTGOING;
        } else {
            $queueCalls->lastActiveQueue = QueueCalls::LAST_ACTIVE_ACTIVE;
        }

        return $queueCalls;
    }

    /**
     * @param ActiveQueueCall[] $calls
     * @return ActiveConference[]
     */
    private function getActiveConferences($calls): array
    {
        $conferences = [];
        foreach ($calls as $call) {
            if ($data = ConferenceDataService::getDataBySid($call->conferenceSid)) {
                $participants = [];
                foreach ($data['participants'] as $key => $part) {
                    if (!$part['userId'] || $part['userId'] === $this->userId) {
                        unset($part['userId']);
                        $participants[] = $part;
                    }
                }
                $conferences[] = new ActiveConference([
                    'sid' => $data['conference']['sid'],
                    'duration' => $data['conference']['duration'],
                    'participants' => $participants,
                    'recordingDisabled' => $data['conference']['recordingDisabled'],
                ]);
            }
        }
        return $conferences;
    }

    private function getActiveCalls(?string $excludeCallSid): array
    {
        $calls = [];
        $last_time = 0;

        $conferenceBase = (bool)(\Yii::$app->params['settings']['voip_conference_base'] ?? false);

        if ($conferenceBase) {
            $query = Call::find()
                ->with(['cProject', 'cClient'])
                ->joinWith(['currentParticipant'])
                ->byCreatedUser($this->userId)
                ->inProgress()
                ->andWhere(['cp_type_id' => [ConferenceParticipant::TYPE_AGENT, ConferenceParticipant::TYPE_USER]]);

            if ($excludeCallSid) {
                $query = $query->andWhere(['<>', 'c_call_sid', $excludeCallSid]);
            }

            $queue = $query->orderBy(['c_updated_dt' => SORT_ASC])->all();
        } else {
            $query = Call::find()
                ->with(['cProject', 'cClient'])
                ->byCreatedUser($this->userId)
                ->andWhere([
                    'OR',
                    ['c_status_id' => Call::STATUS_IN_PROGRESS],
                    ['c_status_id' => Call::STATUS_RINGING]
                ]);

            if ($excludeCallSid) {
                $query = $query->andWhere(['<>', 'c_call_sid', $excludeCallSid]);
            }

            $queue = $query->orderBy(['c_updated_dt' => SORT_ASC])->all();
            foreach ($queue as $key => $call) {
                if ($call->isStatusRinging()) {
                    if ($call->isIn()) {
                        unset($queue[$key]);
                    }
                    if ($call->isOut()) {
                        $child = Call::find()->firstChild($call->c_id)->inProgress()->one();
                        if (!$child) {
                            unset($queue[$key]);
                        }
                    }
                }
                if ($call->isOut() && $call->isStatusInProgress()) {
                    unset($queue[$key]);
                }
            }
        }

        foreach ($queue as $call) {
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

            $callAntiSpamData = $this->getTrustSpamData($call);

            //todo remove after removed not conference call
            $call->c_status_id = Call::STATUS_IN_PROGRESS;
            $calls[] = new ActiveQueueCall([
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
                'isConferenceCreator' => Conference::find()->andWhere(['cf_id' => $call->c_conference_id, 'cf_status_id' => Conference::STATUS_START, 'cf_created_user_id' => $this->userId])->exists(),
                'canContactDetails' => $this->canContactDetails,
                'canCallInfo' => $this->canCallInfo,
                'isClient' => $call->c_client_id ? $call->cClient->isClient() : false,
                'clientId' => $call->c_client_id,
                'recordingDisabled' => $call->c_recording_disabled ? true : false,
                'id' => $call->c_id,
                'callAntiSpamData' => $callAntiSpamData
            ]);

            $last_time = strtotime($call->c_updated_dt);
        }

        return [
            'calls' => $calls,
            'last_time' => $last_time,
        ];
    }

    /**
     * @param string|null $excludeCallSid
     * @return OutgoingQueueCall[]
     */
    private function getOutgoingCalls(?string $excludeCallSid): array
    {
        $calls = [];
        $last_time = 0;

        $conferenceBase = (bool)(\Yii::$app->params['settings']['voip_conference_base'] ?? false);

        if ($conferenceBase) {
            $query = Call::find()
                ->with(['cProject', 'cClient'])
                ->joinWith(['currentParticipant'])
                ->byCreatedUser($this->userId)
                ->out()
                ->ringing()
                ->andWhere(['cp_type_id' => ConferenceParticipant::TYPE_AGENT]);

            if ($excludeCallSid) {
                $query = $query->andWhere(['<>', 'c_call_sid', $excludeCallSid]);
            }

            $queue = $query->orderBy(['c_updated_dt' => SORT_ASC])->all();
        } else {
            $query = Call::find()
                ->with(['cProject', 'cClient'])
                ->byCreatedUser($this->userId)
                ->out()
                ->ringing();

            if ($excludeCallSid) {
                $query = $query->andWhere(['<>', 'c_call_sid', $excludeCallSid]);
            }

            $queue = $query->orderBy(['c_updated_dt' => SORT_ASC])->all();

            foreach ($queue as $key => $call) {
                if (!$call->isGeneralParent()) {
                    unset($queue[$key]);
                    continue;
                }
                $child = Call::find()->firstChild($call->c_id)->inProgress()->one();
                if ($child) {
                    unset($queue[$key]);
                }
            }
        }

        foreach ($queue as $call) {
            $callAntiSpamData = $this->getTrustSpamData($call);
            $calls[] = new OutgoingQueueCall([
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
                'phone' => $call->c_to,
                'name' => $call->cClient ? $call->cClient->getShortName() : '------',
                'company' => '',
                'department' => $call->c_dep_id ? Department::getName($call->c_dep_id) : '',
                'queue' => '',
                'canContactDetails' => $this->canContactDetails,
                'canCallInfo' => $this->canCallInfo,
                'isClient' => $call->c_client_id ? $call->cClient->isClient() : false,
                'clientId' => $call->c_client_id,
                'recordingDisabled' => $call->c_recording_disabled ? true : false,
                'id' => $call->c_id,
                'callAntiSpamData' => $callAntiSpamData
            ]);
            $last_time = strtotime($call->c_updated_dt);
        }

        return [
            'calls' => $calls,
            'last_time' => $last_time,
        ];
    }

    /**
     * @param string|null $excludeCallSid
     * @param bool $generalLinePriorityIsEnabled
     * @return IncomingQueueCall[]
     */
    private function getIncomingCalls(?string $excludeCallSid, bool $generalLinePriorityIsEnabled): array
    {
        $calls = [];
        $last_time = 0;

        $query = CallUserAccess::find()
            ->with(['cuaCall', 'cuaCall.cProject', 'cuaCall', 'cuaCall.cClient'])
            ->joinWith(['cuaCall'])
            ->where(['cua_user_id' => $this->userId, 'cua_status_id' => [CallUserAccess::STATUS_TYPE_PENDING, CallUserAccess::STATUS_TYPE_WARM_TRANSFER]])
            ->andWhere(['<>', 'c_status_id', Call::STATUS_HOLD]);

        if ($generalLinePriorityIsEnabled) {
            $query = $query
                ->andWhere([
                    'OR',
                    ['cua_status_id' => [CallUserAccess::STATUS_TYPE_WARM_TRANSFER]],
                    ['NOT IN', 'c_source_type_id', [Call::SOURCE_GENERAL_LINE, Call::SOURCE_REDIRECT_CALL]],
                ]);
        }

        if ($excludeCallSid) {
            $query = $query->andWhere(['<>', 'c_call_sid', $excludeCallSid]);
        }

        $queue = $query->orderBy(['cua_updated_dt' => SORT_ASC])->all();

        foreach ($queue as $item) {
            $call = $item->cuaCall;

            // this call is priority line call for this user
            if ($call->c_source_type_id === Call::SOURCE_DIRECT_CALL && $call->c_created_user_id !== $item->cua_user_id) {
                continue;
            }

            $callAntiSpamData = $this->getTrustSpamData($call);

            $calls[] = new IncomingQueueCall([
                'callSid' => $call->c_call_sid,
                'conferenceSid' => $call->c_conference_sid,
                'status' => $call->getStatusName(),
                'duration' => time() - strtotime($call->c_updated_dt),
                'leadId' => $call->c_lead_id,
                'typeId' => $item->isWarmTransfer() ? Call::CALL_TYPE_IN : $call->c_call_type_id,
                'type' => $item->isWarmTransfer() ? 'Incoming' : CallHelper::getTypeDescription($call),
                'source_type_id' => $item->isWarmTransfer() ? Call::SOURCE_DIRECT_CALL : $call->c_source_type_id,
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
                'source' => $item->isWarmTransfer() ? Call::SOURCE_LIST[Call::SOURCE_DIRECT_CALL] : ($call->c_source_type_id ? $call->getSourceName() : ''),
                'phone' => $call->c_from,
                'name' => $call->cClient ? $call->cClient->getShortName() : '------',
                'company' => '',
                'department' => $call->c_dep_id ? Department::getName($call->c_dep_id) : '',
                'queue' => $item->isWarmTransfer() ? Call::QUEUE_DIRECT : Call::getQueueName($call),
                'canContactDetails' => $this->canContactDetails,
                'canCallInfo' => $this->canCallInfo,
                'isClient' => $call->c_client_id ? $call->cClient->isClient() : false,
                'clientId' => $call->c_client_id,
                'recordingDisabled' => $call->c_recording_disabled ? true : false,
                'isWarmTransfer' => $item->isWarmTransfer(),
                'id' => $call->c_id,
                'callAntiSpamData' => $callAntiSpamData
            ]);
            $last_time = strtotime($item->cua_updated_dt);
        }

        return [
            'calls' => $calls,
            'last_time' => $last_time,
        ];
    }

    /**
     * @param string|null $excludeCallSid
     * @return IncomingQueueCall[]
     */
    private function getHoldCalls(?string $excludeCallSid): array
    {
        $calls = [];

        $query = CallUserAccess::find()
            ->with(['cuaCall', 'cuaCall.cProject', 'cuaCall', 'cuaCall.cClient'])
            ->joinWith(['cuaCall'])
            ->where(['cua_user_id' => $this->userId, 'cua_status_id' => CallUserAccess::STATUS_TYPE_PENDING])
            ->andWhere(['c_status_id' => Call::STATUS_HOLD]);

        if ($excludeCallSid) {
            $query = $query->andWhere(['<>', 'c_call_sid', $excludeCallSid]);
        }

        $queue = $query->orderBy(['cua_updated_dt' => SORT_ASC])->all();

        foreach ($queue as $item) {
            $call = $item->cuaCall;
            $phone = '';
            if ($call->isIn()) {
                $phone = $call->c_from;
            } elseif ($call->isOut()) {
                $phone = $call->c_to;
            }
            $callAntiSpamData = $this->getTrustSpamData($call);
            $calls[] = new IncomingQueueCall([
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
                'canContactDetails' => $this->canContactDetails,
                'canCallInfo' => $this->canCallInfo,
                'isClient' => $call->c_client_id ? $call->cClient->isClient() : false,
                'clientId' => $call->c_client_id,
                'recordingDisabled' => $call->c_recording_disabled ? true : false,
                'id' => $call->c_id,
                'callAntiSpamData' => $callAntiSpamData
            ]);
        }

        return $calls;
    }

    /**
     * @param bool $generalLinePriorityIsEnabled
     * @return PriorityQueueCall[]
     */
    private function getPriorityCalls(bool $generalLinePriorityIsEnabled): array
    {
        $calls = [];

        if (!$generalLinePriorityIsEnabled) {
            return $calls;
        }

        $queue = CallUserAccess::find()
            ->select([
                'count(*) as count',
                Project::tableName() . '.name as project',
                'dep_name as department',
            ])
            ->innerJoin(Call::tableName(), 'c_id = cua_call_id')
            ->innerJoin(Project::tableName(), 'c_project_id = ' . Project::tableName() . '.id')
            ->innerJoin(Department::tableName(), 'c_dep_id = dep_id')
            ->andWhere([
                'cua_user_id' => $this->userId,
                'cua_status_id' => CallUserAccess::STATUS_TYPE_PENDING
            ])
            ->andWhere([
                'OR',
                ['c_source_type_id' => [Call::SOURCE_GENERAL_LINE, Call::SOURCE_REDIRECT_CALL]],
                [
                    'AND',
                    ['c_source_type_id' => [Call::SOURCE_DIRECT_CALL]],
                    ['<>', 'c_created_user_id', new Expression('cua_user_id')],
                ],
            ])
            ->andWhere(['<>', 'c_status_id', Call::STATUS_HOLD])
            ->groupBy(['c_project_id', 'c_dep_id'])
            ->asArray()
            ->all();

        foreach ($queue as $item) {
            $calls[] = new PriorityQueueCall([
                'count' => (int)$item['count'],
                'project' => $item['project'],
                'department' => $item['department'],
            ]);
        }

        return $calls;
    }

    private function getTrustSpamData(Call $call): array
    {
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
        return $callAntiSpamData;
    }
}

<?php

namespace sales\model\call\services\currentQueueCalls;

use common\models\Call;
use common\models\CallUserAccess;
use common\models\Department;
use common\models\Lead;
use common\models\Project;
use sales\model\conference\service\ConferenceDataService;
use sales\model\leadRedial\entity\CallRedialUserAccess;
use yii\db\Expression;

class CurrentQueueCallsService
{
    public function getQueuesCalls(int $userId, ?string $excludeCallSid, bool $generalLinePriorityIsEnabled): QueueCalls
    {
        $auth = \Yii::$app->authManager;
        $canContactDetails = $auth->checkAccess($userId, '/client/ajax-get-info');
        $canCallInfo = $auth->checkAccess($userId, '/call/ajax-call-info');

        $priorityQueue = $this->getPriorityCalls($generalLinePriorityIsEnabled, $userId);
        $holdQueue = $this->getHoldCalls($excludeCallSid, $userId, $canContactDetails, $canCallInfo);
        $incomingQueue = $this->getIncomingCalls($excludeCallSid, $generalLinePriorityIsEnabled, $userId, $canContactDetails, $canCallInfo);
        $outgoingQueue = $this->getOutgoingCalls($excludeCallSid, $userId, $canContactDetails, $canCallInfo);
        $activeQueue = $this->getActiveCalls($excludeCallSid, $userId, $canContactDetails, $canCallInfo);
        $conferences = $this->getActiveConferences($activeQueue['calls'], $userId);

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
     * @param int $currentUserId
     * @return ActiveConference[]
     */
    private function getActiveConferences(array $calls, int $currentUserId): array
    {
        $conferences = [];
        foreach ($calls as $call) {
            if ($data = ConferenceDataService::getDataBySid($call->conferenceSid)) {
                $participants = [];
                foreach ($data['participants'] as $key => $part) {
                    if (!$part['userId'] || $part['userId'] === $currentUserId) {
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

    private function getActiveCalls(?string $excludeCallSid, int $currentUserId, bool $canContactDetails, bool $canCallInfo): array
    {
        $calls = [];
        $last_time = 0;

        $query = Call::find()
            ->with(['cProject', 'cClient'])
            ->byCreatedUser($currentUserId)
            ->andWhere(['c_status_id' => Call::STATUS_IN_PROGRESS]);

        if ($excludeCallSid) {
            $query = $query->andWhere(['<>', 'c_call_sid', $excludeCallSid]);
        }

        $queue = $query->orderBy(['c_updated_dt' => SORT_ASC])->all();
        foreach ($queue as $key => $call) {
            $creatorType = $call->getDataCreatorType();
            if (!$creatorType->isAgent() && !$creatorType->isUser()) {
                unset($queue[$key]);
                continue;
            }
            if ($creatorType->isUser() && $call->isGeneralParent()) {
                $childIsRinging = Call::find()->byParentId($call->c_id)->ringing()->exists();
                if ($childIsRinging) {
                    unset($queue[$key]);
        		}
        	}
        }

        foreach ($queue as $call) {
            $calls[] = ActiveQueueCall::create(
                $call,
                $currentUserId,
                $canContactDetails,
                $canCallInfo,
                $this->getTrustSpamData($call)
            );
            $last_time = strtotime($call->c_updated_dt);
        }

        return [
            'calls' => $calls,
            'last_time' => $last_time,
        ];
    }

    /**
     * @param string|null $excludeCallSid
     * @param int $currentUserId
     * @param bool $canContactDetails
     * @param bool $canCallInfo
     * @return OutgoingQueueCall[]
     */
    private function getOutgoingCalls(?string $excludeCallSid, int $currentUserId, bool $canContactDetails, bool $canCallInfo): array
    {
        $calls = [];
        $last_time = 0;

        $query = Call::find()
            ->with(['cProject', 'cClient'])
            ->byCreatedUser($currentUserId)
            ->out()
            ->andWhere(['c_status_id' => [Call::STATUS_RINGING, Call::STATUS_IN_PROGRESS]]);

        if ($excludeCallSid) {
            $query = $query->andWhere(['<>', 'c_call_sid', $excludeCallSid]);
        }

        $queue = $query->orderBy(['c_updated_dt' => SORT_ASC])->all();

        foreach ($queue as $key => $call) {
            $creatorType = $call->getDataCreatorType();
            if (!$creatorType->isAgent() && !$creatorType->isUser()) {
                unset($queue[$key]);
                continue;
            }
            if ($call->isStatusInProgress()) {
                if ($creatorType->isUser() && $call->isGeneralParent()) {
                    $childIsRinging = Call::find()->byParentId($call->c_id)->ringing()->exists();
                    if ($childIsRinging) {
                        $call->c_status_id = Call::STATUS_RINGING;
                    } else {
                        unset($queue[$key]);
                    }
                } else {
                    unset($queue[$key]);
        		}
        	}
        }

        foreach ($queue as $call) {
            $calls[] = OutgoingQueueCall::create(
                $call,
                $canContactDetails,
                $canCallInfo,
                $this->getTrustSpamData($call)
            );
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
     * @param int $currentUserId
     * @param bool $canContactDetails
     * @param bool $canCallInfo
     * @return IncomingQueueCall[]
     */
    private function getIncomingCalls(?string $excludeCallSid, bool $generalLinePriorityIsEnabled, int $currentUserId, bool $canContactDetails, bool $canCallInfo): array
    {
        $calls = [];
        $last_time = 0;

        $query = CallUserAccess::find()
            ->with(['cuaCall', 'cuaCall.cProject', 'cuaCall', 'cuaCall.cClient'])
            ->joinWith(['cuaCall'])
            ->where(['cua_user_id' => $currentUserId, 'cua_status_id' => [CallUserAccess::STATUS_TYPE_PENDING, CallUserAccess::STATUS_TYPE_WARM_TRANSFER]])
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

            $calls[] = IncomingQueueCall::createIncoming(
                $item,
                $canContactDetails,
                $canCallInfo,
                $this->getTrustSpamData($call)
            );
            $last_time = strtotime($item->cua_updated_dt);
        }

        return [
            'calls' => $calls,
            'last_time' => $last_time,
        ];
    }

    /**
     * @param string|null $excludeCallSid
     * @param int $currentUserId
     * @param bool $canContactDetails
     * @param bool $canCallInfo
     * @return IncomingQueueCall[]
     */
    private function getHoldCalls(?string $excludeCallSid, int $currentUserId, bool $canContactDetails, bool $canCallInfo): array
    {
        $calls = [];

        $query = CallUserAccess::find()
            ->with(['cuaCall', 'cuaCall.cProject', 'cuaCall', 'cuaCall.cClient'])
            ->joinWith(['cuaCall'])
            ->where(['cua_user_id' => $currentUserId, 'cua_status_id' => CallUserAccess::STATUS_TYPE_PENDING])
            ->andWhere(['c_status_id' => Call::STATUS_HOLD]);

        if ($excludeCallSid) {
            $query = $query->andWhere(['<>', 'c_call_sid', $excludeCallSid]);
        }

        $queue = $query->orderBy(['cua_updated_dt' => SORT_ASC])->all();

        foreach ($queue as $item) {
            $calls[] = IncomingQueueCall::createHold(
                $item,
                $canContactDetails,
                $canCallInfo,
                $this->getTrustSpamData($item->cuaCall)
            );
        }

        return $calls;
    }

    /**
     * @param bool $generalLinePriorityIsEnabled
     * @param int $currentUserId
     * @return PriorityQueueCall[]
     */
    private function getPriorityCalls(bool $generalLinePriorityIsEnabled, int $currentUserId): array
    {
        $calls = [];

        if (!$generalLinePriorityIsEnabled) {
            return $calls;
        }

        $defaultQueue = CallUserAccess::find()
            ->select([
                'count(*) as count',
                Project::tableName() . '.name as project',
                'dep_name as department',
            ])
            ->innerJoin(Call::tableName(), 'c_id = cua_call_id')
            ->innerJoin(Project::tableName(), 'c_project_id = ' . Project::tableName() . '.id')
            ->innerJoin(Department::tableName(), 'c_dep_id = dep_id')
            ->andWhere([
                'cua_user_id' => $currentUserId,
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
            ->indexBy(function ($raw) {
                return $raw['project'] . '.' . $raw['department'];
            })
            ->asArray()
            ->all();

        $redialQueue = CallRedialUserAccess::find()
            ->select([
                'count(*) as count',
                Project::tableName() . '.name as project',
                'dep_name as department',
            ])
            ->innerJoin(Lead::tableName(), Lead::tableName() . '.id = crua_lead_id')
            ->innerJoin(Project::tableName(), Project::tableName() . '.id = ' . Lead::tableName() . '.project_id')
            ->innerJoin(Department::tableName(), Department::tableName() . '.dep_id = ' . Lead::tableName() . '.l_dep_id')
            ->andWhere(['crua_user_id' => $currentUserId])
            ->withoutExpired()
            ->groupBy([Lead::tableName() . '.project_id', Lead::tableName() . '.l_dep_id'])
            ->indexBy(function ($raw) {
                return $raw['project'] . '.' . $raw['department'];
            })
            ->asArray()
            ->all();

        foreach ($defaultQueue as $call) {
            $key = $call['project'] . '.' . $call['department'];
            if (isset($redialQueue[$key])) {
                $calls[] = new PriorityQueueCall([
                    'count' => (int)$call['count'] + (int)$redialQueue[$key]['count'],
                    'project' => $call['project'],
                    'department' => $call['department'],
                ]);
                unset($redialQueue[$key]);
            } else {
                $calls[] = new PriorityQueueCall([
                    'count' => (int)$call['count'],
                    'project' => $call['project'],
                    'department' => $call['department'],
                ]);
            }
        }

        foreach ($redialQueue as $call) {
            $calls[] = new PriorityQueueCall([
                'count' => (int)$call['count'],
                'project' => $call['project'],
                'department' => $call['department'],
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

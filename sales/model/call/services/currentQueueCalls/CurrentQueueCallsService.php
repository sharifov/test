<?php

namespace sales\model\call\services\currentQueueCalls;

use common\models\Call;
use common\models\CallUserAccess;
use common\models\ConferenceParticipant;
use common\models\Department;
use sales\model\call\helper\CallHelper;
use sales\model\phoneList\entity\PhoneList;

class CurrentQueueCallsService
{
    private int $userId;

    public function getQueuesCalls(int $userId): QueueCalls
    {
        $this->userId = $userId;

        $holdQueue = $this->getHoldCalls();
        $incomingQueue = $this->getIncomingCalls();
        $outgoingQueue = $this->getOutgoingCalls();
        $activeQueue = $this->getActiveCalls();

        $queueCalls = new QueueCalls(
            $holdQueue,
            $incomingQueue['calls'],
            $outgoingQueue['calls'],
            $activeQueue['calls'],
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
     * @return ActiveQueueCall[]
     */
    private function getActiveCalls(): array
    {
        $calls = [];
        $last_time = 0;

        $conferenceBase = (bool)(\Yii::$app->params['settings']['voip_conference_base'] ?? false);

        if ($conferenceBase) {
            $queue = Call::find()
                ->with(['cProject', 'cClient'])
                ->joinWith(['currentParticipant'])
                ->byCreatedUser($this->userId)
                ->inProgress()
                ->andWhere(['cp_type_id' => ConferenceParticipant::TYPE_AGENT])
                ->orderBy(['c_updated_dt' => SORT_ASC])
                ->all();
        } else {
            $queue = Call::find()
                ->with(['cProject', 'cClient'])
                ->byCreatedUser($this->userId)
                ->andWhere(['OR', ['c_status_id' => Call::STATUS_IN_PROGRESS], ['c_status_id' => Call::STATUS_RINGING]])
                ->orderBy(['c_updated_dt' => SORT_ASC])
                ->all();
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

            if ($call->isIn() || $call->isOut()) {
                $name = $call->cClient ? $call->cClient->getFullName() : '------';
            } elseif ($call->isJoin() && ($parentJoin = $call->cParent) && $parentJoin->cCreatedUser) {
                $name = $parentJoin->cCreatedUser->username;
            } else {
                $name = '------';
            }

            $phone = '';
            if ($call->isIn() ) {
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
            $isHold = false;
            $holdDuration = 0;
            if ($call->currentParticipant && $call->currentParticipant->isHold()) {
                $isHold = true;
                $holdDuration = time() - strtotime($call->currentParticipant->cp_hold_dt);
            }

            $isInternal = PhoneList::find()->byPhone($call->c_from)->enabled()->exists();

            //todo remove after removed not conference call
            $call->c_status_id = Call::STATUS_IN_PROGRESS;
            $calls[] = new ActiveQueueCall([
                'callSid' => $call->c_call_sid,
                'status' => $call->getStatusName(),
                'leadId' => $call->c_lead_id,
                'source_type_id' => $call->c_source_type_id,
                'fromInternal' => $isInternal,
                'isMute' => $isMute,
                'isListen' => $isListen,
                'isHold' => $isHold,
                'holdDuration' => $holdDuration,
                'typeId' => $call->c_call_type_id,
                'type' => CallHelper::getTypeDescription($call),
                'phone' => $phone,
                'name' => $name,
                'company' => '',
                'duration' => time() - strtotime($call->c_updated_dt),
                'project' => $call->c_project_id ? $call->cProject->name : '',
                'source' => $call->c_source_type_id ? $call->getSourceName() : '',
                'department' => $call->c_dep_id ? Department::getName($call->c_dep_id) : '',
            ]);

            $last_time = strtotime($call->c_updated_dt);
        }

        return [
            'calls' => $calls,
            'last_time' => $last_time,
        ];
    }

    /**
     * @return OutgoingQueueCall[]
     */
    private function getOutgoingCalls(): array
    {
        $calls = [];
        $last_time = 0;

        $conferenceBase = (bool)(\Yii::$app->params['settings']['voip_conference_base'] ?? false);

        if ($conferenceBase) {
            $queue = Call::find()
                ->with(['cProject', 'cClient'])
                ->joinWith(['currentParticipant'])
                ->byCreatedUser($this->userId)
                ->out()
                ->ringing()
                ->andWhere(['cp_type_id' => ConferenceParticipant::TYPE_AGENT])
                ->orderBy(['c_updated_dt' => SORT_ASC])
                ->all();
        } else {
            $queue = Call::find()
                ->with(['cProject', 'cClient'])
                ->byCreatedUser($this->userId)
                ->out()
                ->ringing()
                ->orderBy(['c_updated_dt' => SORT_ASC])
                ->all();

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
            $calls[] = new OutgoingQueueCall([
                'callSid' => $call->c_call_sid,
                'type' => CallHelper::getTypeDescription($call),
                'status' => $call->getStatusName(),
                'duration' => time() - strtotime($call->c_updated_dt),
                'project' => $call->c_project_id ? $call->cProject->name : '',
                'source' => $call->c_source_type_id ? $call->getSourceName() : '',
                'name' => $call->cClient ? $call->cClient->getFullName() : '------',
                'phone' => $call->c_to,
                'company' => '',
                'department' => $call->c_dep_id ? Department::getName($call->c_dep_id) : '',
            ]);
            $last_time = strtotime($call->c_updated_dt);
        }

        return [
            'calls' => $calls,
            'last_time' => $last_time,
        ];
    }

    /**
     * @return IncomingQueueCall[]
     */
    private function getIncomingCalls(): array
    {
        $calls = [];
        $last_time = 0;

        $queue = CallUserAccess::find()
            ->with(['cuaCall', 'cuaCall.cProject', 'cuaCall', 'cuaCall.cClient'])
            ->joinWith(['cuaCall'])
            ->where(['cua_user_id' => $this->userId, 'cua_status_id' => CallUserAccess::STATUS_TYPE_PENDING])
            ->andWhere(['<>', 'c_status_id', Call::STATUS_HOLD])
            ->orderBy(['cua_updated_dt' => SORT_ASC])
            ->all();

        foreach ($queue as $item) {
            $call = $item->cuaCall;
            $calls[] = new IncomingQueueCall([
                'callSid' => $call->c_call_sid,
                'status' => $call->getStatusName(),
                'duration' => time() - strtotime($call->c_updated_dt),
                'typeId' => $call->c_call_type_id,
                'type' => CallHelper::getTypeDescription($call),
                'source_type_id' => $call->c_source_type_id,
                'fromInternal' => false,
                'isHold' => false,
                'holdDuration' => 0,
                'isListen' => false,
                'isMute' => false,
                'project' => $call->c_project_id ? $call->cProject->name : '',
                'source' => $call->c_source_type_id ? $call->getSourceName() : '',
                'phone' => $call->c_from,
                'name' => $call->cClient ? $call->cClient->getFullName() : '------',
                'company' => '',
                'department' => $call->c_dep_id ? Department::getName($call->c_dep_id) : '',
                'queue' => Call::getQueueName($call),
            ]);
            $last_time = strtotime($item->cua_updated_dt);
        }

        return [
            'calls' => $calls,
            'last_time' => $last_time,
        ];
    }

    /**
     * @return IncomingQueueCall[]
     */
    private function getHoldCalls(): array
    {
        $calls = [];

        $queue = CallUserAccess::find()
            ->with(['cuaCall', 'cuaCall.cProject', 'cuaCall', 'cuaCall.cClient'])
            ->joinWith(['cuaCall'])
            ->where(['cua_user_id' => $this->userId, 'cua_status_id' => CallUserAccess::STATUS_TYPE_PENDING])
            ->andWhere(['c_status_id' => Call::STATUS_HOLD])
            ->orderBy(['cua_updated_dt' => SORT_ASC])
            ->all();

        foreach ($queue as $item) {
            $call = $item->cuaCall;
            $phone = '';
            if ($call->isIn()) {
                $phone = $call->c_from;
            } elseif ($call->isOut()) {
                $phone = $call->c_to;
            }
            $calls[] = new IncomingQueueCall([
                'callSid' => $call->c_call_sid,
                'status' => $call->getStatusName(),
                'duration' => time() - strtotime($call->c_updated_dt),
                'typeId' => $call->c_call_type_id,
                'type' => CallHelper::getTypeDescription($call),
                'source_type_id' => $call->c_source_type_id,
                'fromInternal' => false,
                'isHold' => false,
                'holdDuration' => 0,
                'isListen' => false,
                'isMute' => false,
                'project' => $call->c_project_id ? $call->cProject->name : '',
                'source' => $call->c_source_type_id ? $call->getSourceName() : '',
                'phone' => $phone,
                'name' => $call->cClient ? $call->cClient->getFullName() : '------',
                'company' => '',
                'department' => $call->c_dep_id ? Department::getName($call->c_dep_id) : '',
                'queue' => Call::getQueueName($call),
            ]);
        }

      return $calls;
    }
}

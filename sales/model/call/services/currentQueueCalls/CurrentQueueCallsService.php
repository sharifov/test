<?php

namespace sales\model\call\services\currentQueueCalls;

use common\models\Call;
use common\models\CallUserAccess;
use common\models\ConferenceParticipant;
use sales\model\call\helper\CallHelper;

class CurrentQueueCallsService
{
    private int $userId;

    public function getQueuesCalls(int $userId): QueueCalls
    {
        $this->userId = $userId;

        $incomingQueue = $this->getIncomingCalls();
        $outgoingQueue = $this->getOutgoingCalls();
        $activeQueue = $this->getActiveCalls();

        $queueCalls = new QueueCalls(
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

        $queue = Call::find()
            ->with(['cProject', 'cClient'])
            ->joinWith(['currentParticipant'])
            ->byCreatedUser($this->userId)
            ->inProgress()
            ->andWhere(['cp_type_id' => ConferenceParticipant::TYPE_AGENT])
            ->orderBy(['c_updated_dt' => SORT_ASC])
            ->all();

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
                    $phone = $parentJoin->c_from;
                }
            }

            $isMute = false;
            if ($call->currentParticipant && $call->currentParticipant->isMute()) {
                $isMute = true;
            }
            if ($call->isJoin() && $call->c_source_type_id === Call::SOURCE_LISTEN) {
                $isMute = true;
            }

            $calls[] = new ActiveQueueCall([
                'callId' => $call->c_id,
                'isMute' => $isMute,
                'isListen' => $call->c_source_type_id === Call::SOURCE_LISTEN,
                'typeId' => $call->c_call_type_id,
                'type' => CallHelper::getTypeDescription($call),
                'phone' => $phone,
                'name' => $name,
                'duration' => time() - strtotime($call->c_updated_dt),
                'projectName' => $call->c_project_id ? $call->cProject->name : '',
                'sourceName' => $call->c_source_type_id ? $call->getSourceName() : '',
                'isHold' => $call->currentParticipant && $call->currentParticipant->isHold(),
                'holdDuration' => $call->currentParticipant && $call->currentParticipant->isHold() ? (time() - strtotime($call->currentParticipant->cp_hold_dt)) : 0,
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

        $queue = Call::find()
            ->with(['cProject', 'cClient'])
            ->joinWith(['currentParticipant'])
            ->byCreatedUser($this->userId)
            ->out()
            ->ringing()
            ->andWhere(['cp_type_id' => ConferenceParticipant::TYPE_AGENT])
            ->orderBy(['c_updated_dt' => SORT_ASC])
            ->all();

        foreach ($queue as $call) {
            $calls[] = new OutgoingQueueCall([
                'callId' => $call->c_id,
                'phone' => $call->c_to,
                'sourceName' => $call->c_source_type_id ? $call->getSourceName() : '',
                'projectName' => $call->c_project_id ? $call->cProject->name : '',
                'name' => $call->cClient ? $call->cClient->getFullName() : '------',
                'type' => CallHelper::getTypeDescription($call),
                'status' => $call->getStatusName(),
                'duration' => time() - strtotime($call->c_updated_dt)
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
            ->with(['cuaCall', 'cuaCall.cProject', 'cuaCall.currentParticipant', 'cuaCall.cClient'])
            ->joinWith(['cuaCall'])
            ->where(['cua_user_id' => $this->userId, 'cua_status_id' => CallUserAccess::STATUS_TYPE_PENDING])
            ->andWhere(['<>', 'c_status_id', Call::STATUS_HOLD])
            ->orderBy(['cua_updated_dt' => SORT_ASC])
            ->all();

        foreach ($queue as $item) {
            $call = $item->cuaCall;
            $calls[] = new IncomingQueueCall([
                'callId' => $call->c_id,
                'sourceName' => $call->c_source_type_id ? $call->getSourceName() : '',
                'phone' => $call->c_from,
                'name' => $call->cClient ? $call->cClient->getFullName() : '------',
                'projectName' => $call->c_project_id ? $call->cProject->name : '',
                'type' => CallHelper::getTypeDescription($call),
                'fromInternal' => false
            ]);
            $last_time = strtotime($item->cua_updated_dt);
        }

        return [
            'calls' => $calls,
            'last_time' => $last_time,
        ];
    }
}

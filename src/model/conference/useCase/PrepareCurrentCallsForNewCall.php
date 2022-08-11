<?php

namespace src\model\conference\useCase;

use common\components\CommunicationService;
use common\models\Call;
use common\models\Conference;
use common\models\ConferenceParticipant;
use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use yii\db\Expression;
use yii\helpers\VarDumper;

/**
 * Class PrepareCurrentCallsForNewCall
 *
 * @property int $userId
 * @property CommunicationService $communication
 * @property array $messages
 */
class PrepareCurrentCallsForNewCall
{
    private int $userId;
    private CommunicationService $communication;
    private array $messages = [];

    public function __construct(int $userId)
    {
        $this->userId = $userId;
        $this->communication = \Yii::$app->comms;
    }

    public function prepare(?string $holdAnnounce = null): bool
    {
        foreach ($this->getCallsForActions() as $call) {
            if ($call['action'] === 'hangup') {
                $this->hangUp($call['call_sid']);
            } elseif ($call['action'] === 'hold') {
                $clientParticipant = ConferenceParticipant::find()
                    ->andWhere([
                        'cp_cf_id' => $call['conference_id'],
                        'cp_type_id' => ConferenceParticipant::TYPE_CLIENT,
                    ])
                    ->andWhere(['IS NOT', 'cp_status_id', null])
                    ->andWhere(['<>', 'cp_status_id', ConferenceParticipant::STATUS_LEAVE])
                    ->one();
                if ($clientParticipant) {
                    $this->toHold($clientParticipant, $call['conference_sid'], $call['call_sid'], $holdAnnounce);
                } else {
                    $this->hangUp($call['call_sid']);
                }
            }
        }

        if ($this->messages) {
            $this->log();
            return false;
        }

        return true;
    }

    private function getCallsForActions(): array
    {
        return Call::find()
            ->select(['c_call_sid as call_sid', 'cf_sid as conference_sid', 'cf_id as conference_id'])
            ->addSelect(new Expression(
                "if (
                                (cp_id is not null AND cf_id is not null AND cp_type_id <> " . ConferenceParticipant::TYPE_USER . "), 
                                'hold', 
                                if (
                                    ((cp_id is null AND cf_id is not null) OR (c_source_type_id = '" . Call::SOURCE_INTERNAL . "' AND c_status_id = '" . Call::STATUS_RINGING . "')),
                                     'no action',
                                     'hangup'
                                )
                           ) as action"
            ))
            ->andWhere([
            'c_created_user_id' => $this->userId,
            'c_status_id' => [Call::STATUS_RINGING, Call::STATUS_IN_PROGRESS]
        ])
        ->leftJoin(ConferenceParticipant::tableName(), 'cp_call_id = c_id AND (cp_type_id = ' . ConferenceParticipant::TYPE_AGENT . ' OR cp_type_id = ' . ConferenceParticipant::TYPE_USER . ') AND cp_status_id IS NOT NULL AND cp_status_id <> ' . ConferenceParticipant::STATUS_LEAVE)
        ->leftJoin(Conference::tableName(), 'cf_created_user_id = c_created_user_id AND cf_id = c_conference_id AND cf_status_id = ' . Conference::STATUS_START)
        ->asArray()->all();
    }

    public function toHold(ConferenceParticipant $clientParticipant, string $conferenceSid, string $callSid, ?string $announce): void
    {
        try {
            $result = $this->communication->disconnectFromConferenceCall($conferenceSid, $callSid, $announce);
            $isError = (bool)($result['error'] ?? true);
            if (!$isError) {
                $this->sendSocketMessageCompleteCall($callSid);
                $this->transferClientCallToHold($clientParticipant, $callSid);
            } else {
                $this->addMessage([
                    'title' => 'To Hold',
                    'user_id' => $this->userId,
                    'call_sid' => $callSid,
                    'error' => $result['message'],
                ]);
            }
        } catch (\Throwable $e) {
            $this->addMessage([
                'title' => 'To Hold',
                'user_id' => $this->userId,
                'call_sid' => $callSid,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function transferClientCallToHold(ConferenceParticipant $participantClient, string $callSid): void
    {
        if (!$clientCall = $participantClient->cpCall) {
            \Yii::info([
                'message' => 'Not found relation client-participant with client-call',
                'comment' => 'Possible participant callback received before call callback',
                'user_id' => $this->userId,
                'participantId' => $participantClient->cp_id,
                'participantCallSid' => $participantClient->cp_call_sid,
                'agentCallSid' => $callSid,
            ], 'log\PrepareCurrentCallsForNewCall:transferClientCallToHold');
            return;
        }

        if ($clientCall->isTwFinishStatus()) {
            \Yii::info([
                'message' => 'Client call is already finished. Client call will not be added to hold.',
                'userId' => $this->userId,
                'agentCallSid' => $callSid,
                'clientCall' => $clientCall->getAttributes(),
            ], 'log\PrepareCurrentCallsForNewCall:transferClientCallToHold');
            return;
        }

        if ($clientCall->isOut()) {
            if ($parent = $clientCall->cParent) {
                $parent->c_queue_start_dt = date('Y-m-d H:i:s');

                if (!$clientCall->c_group_id) {
                    $parent->c_group_id = $parent->c_id;
                }

                if (!$parent->save()) {
                    $this->addMessage([
                        'title' => 'Transfer client call to hold',
                        'user_id' => $this->userId,
                        'calls_sid' => $callSid,
                        'error' => 'Cant change Parent Call: ' . VarDumper::dumpAsString($parent->getErrors()),
                    ]);
                    return;
                }
            } else {
                \Yii::error([
                    'message' => 'Not found parent call',
                    'clientCall' => $clientCall->getAttributes(),
                ], 'PrepareCurrentCallsForNewCall:transferClientCallToHold');
            }
        } elseif ($clientCall->isIn()) {
            if (!$clientCall->c_group_id) {
                if ($currentCall = Call::findOne(['c_call_sid' => $callSid])) {
                    $currentCall->c_group_id = $currentCall->c_id;
                    if (!$currentCall->save()) {
                        \Yii::error(VarDumper::dumpAsString([
                            'message' => 'Cant save current call',
                            'currentCall' => $currentCall->getAttributes(),
                            'errors' => $currentCall->getErrors(),
                        ]), 'PrepareCurrentCallsForNewCall:transferClientCallToHold');
                    }
                    $clientCall->c_group_id = $currentCall->c_id;
                } else {
                    \Yii::error(VarDumper::dumpAsString([
                        'message' => 'Not found agent call',
                        'agentCallSId' => $callSid,
                        'clientCall' => $clientCall->getAttributes(),
                    ]), 'PrepareCurrentCallsForNewCall:transferClientCallToHold');
                }
            }
            $clientCall->c_queue_start_dt = date('Y-m-d H:i:s');
        }

        $clientCall->hold();
        if (!$clientCall->save()) {
            $this->addMessage([
                'title' => 'Transfer client call to hold',
                'user_id' => $this->userId,
                'calls_sid' => $callSid,
                'error' => 'Cant transfer Client Call To Hold: ' . VarDumper::dumpAsString($clientCall->getErrors()),
            ]);
            return;
        }

        if (Call::applyCallToAgentAccess($clientCall, $this->userId)) {
        }
    }

    private function hangUp(string $callSid): void
    {
        try {
            $result = $this->communication->hangUp($callSid);
            $isError = (bool)($result['error'] ?? true);
            if ($isError) {
                $this->addMessage([
                    'title' => 'Hangup',
                    'user_id' => $this->userId,
                    'call_sid' => $callSid,
                    'error' => $result['message'],
                ]);
            } else {
                $this->sendSocketMessageCompleteCall($callSid);
            }
        } catch (\Throwable $e) {
            $this->addMessage([
                'title' => 'Hangup',
                'user_id' => $this->userId,
                'call_sid' => $callSid,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function addMessage($message): void
    {
        $this->messages[] = $message;
    }

    private function log(): void
    {
        foreach ($this->messages as $message) {
            if ($ntf = Notifications::create($this->userId, $message['title'], $message['error'], Notifications::TYPE_DANGER, true)) {
                $dataNotification = (\Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                Notifications::publish('getNewNotification', ['user_id' => $this->userId], $dataNotification);
            }

            \Yii::error(VarDumper::dumpAsString($message), static::class);
        }
    }

    private function sendSocketMessageCompleteCall(string $callSid): void
    {
        Notifications::publish('completeCall', ['user_id' => $this->userId], [
            'data' => [
                'call' => [
                    'sid' => $callSid,
                    'user_id' => $this->userId,
                ],
            ],
        ]);
    }
}

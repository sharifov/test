<?php

namespace src\model\conference\useCase;

use common\components\CommunicationService;
use common\models\Call;
use common\models\Conference;
use common\models\ConferenceParticipant;
use common\models\Notifications;
use common\models\query\ConferenceParticipantQuery;
use common\models\query\ConferenceQuery;
use frontend\widgets\notification\NotificationMessage;
use yii\helpers\VarDumper;

//todo remove

/**
 * Class DisconnectFromAllActiveCreatedConferences
 *
 * @property CommunicationService $communication
 * @property array $messages
 */
class DisconnectFromAllActiveClientsCreatedConferences
{
    private CommunicationService $communication;
    private array $messages = [];

    public function __construct()
    {
        $this->communication = \Yii::$app->comms;
    }

    public function disconnect(int $userId): bool
    {
        foreach ($this->getActiveCreatedConferenceCalls($userId) as $call) {
            if ($call->c_conference_sid) {
                try {
                    $result = $this->communication->disconnectFromConferenceCall($call->c_conference_sid, $call->c_call_sid);
                    $isError = (bool)($result['error'] ?? true);
                    if (!$isError) {
                        $this->transferCallToHold($call, $userId);
                    } else {
                        $this->addMessage([
                            'user_id' => $userId,
                            'call_id' => $call->c_id,
                            'error' => $result['message'],
                        ]);
                    }
                } catch (\Throwable $e) {
                    $this->addMessage([
                        'user_id' => $userId,
                        'call_id' => $call->c_id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
        if ($this->messages) {
            $this->log($userId);
            return false;
        }
        return true;
    }

    private function transferCallToHold(Call $call, int $userId): void
    {
        $participantClient = ConferenceParticipant::find()->andWhere([
            'cp_cf_id' => $call->c_conference_id,
            'cp_type_id' => ConferenceParticipant::TYPE_CLIENT
        ])->one();

        if (!$participantClient) {
            $this->addMessage([
                'user_id' => $userId,
                'call_id' => $call->c_id,
                'error' => 'Not found Client Participant on Conference SID: ' . $call->c_conference_sid,
            ]);
            return;
        }

        if (!$clientCall = $participantClient->cpCall) {
            $this->addMessage([
                'user_id' => $userId,
                'call_id' => $call->c_id,
                'error' => 'Not found Call Relation on Client Participant ID:' . $participantClient->cp_id,
            ]);
            return;
        }

        $clientCall->hold();
        if (!$clientCall->save()) {
            $this->addMessage([
                'user_id' => $userId,
                'call_id' => $call->c_id,
                'error' => 'Cant transfer Client Call To Hold: ' . VarDumper::dumpAsString($clientCall->getErrors()),
            ]);
            return;
        }

        if (Call::applyCallToAgentAccess($clientCall, $userId)) {
        }
    }

    /**
     * @param int $userId
     * @return Call[]
     */
    private function getActiveCreatedConferenceCalls(int $userId): array
    {
        return Call::find()->andWhere([
            'c_created_user_id' => $userId,
            'c_call_type_id' => [Call::CALL_TYPE_IN, Call::CALL_TYPE_OUT],
            'c_status_id' => [Call::STATUS_RINGING, Call::STATUS_IN_PROGRESS, Call::STATUS_HOLD]
        ])
            ->innerJoinWith(['conferenceParticipants' => static function (ConferenceParticipantQuery $query) {
                $query->andOnCondition([
                    'cp_type_id' => ConferenceParticipant::TYPE_AGENT,
                ]);
                $query->andOnCondition(['IS NOT', 'cp_status_id', null]);
                $query->andOnCondition(['<>', 'cp_status_id', ConferenceParticipant::STATUS_LEAVE]);
            }], false)
            ->innerJoinWith(['conferences' => static function (ConferenceQuery $query) {
                $query->andOnCondition([
                    'cf_status_id' => [
                        Conference::STATUS_START,
                    ],
                ]);
                $query->andOnCondition('cf_id = c_conference_id');
            }], false)->all();
    }

    private function addMessage($message): void
    {
        $this->messages[] = $message;
    }

    private function log(int $userId): void
    {
        foreach ($this->messages as $message) {
            if ($ntf = Notifications::create($userId, 'Hold call', $message['error'], Notifications::TYPE_DANGER, true)) {
                $dataNotification = (\Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                Notifications::publish('getNewNotification', ['user_id' => $userId], $dataNotification);
            }

            \Yii::error(VarDumper::dumpAsString($message), static::class);
        }
    }
}

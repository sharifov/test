<?php

namespace src\model\conference\useCase;

use common\components\CommunicationService;
use common\models\Call;
use common\models\Conference;
use common\models\ConferenceParticipant;
use common\models\Notifications;
use common\models\query\ConferenceParticipantQuery;
use frontend\widgets\notification\NotificationMessage;
use yii\helpers\VarDumper;

/**
 * Class CompleteAllUserToUserCalls
 *
 * @property CommunicationService $communication
 * @property array $messages
 */
class CompleteAllUserToUserCalls
{
    private CommunicationService $communication;
    private array $messages = [];

    public function __construct()
    {
        $this->communication = \Yii::$app->comms;
    }

    public function complete(int $userId): bool
    {
        foreach ($this->getUserToUserCalls($userId) as $call) {
            try {
                $result = $this->communication->hangUp($call->c_call_sid);
                $isError = (bool)($result['error'] ?? true);
                if ($isError) {
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
        if ($this->messages) {
            $this->log($userId);
            return false;
        }
        return true;
    }

    /**
     * @param int $userId
     * @return Call[]
     */
    private function getUserToUserCalls(int $userId): array
    {
        return Call::find()->andWhere([
            'c_created_user_id' => $userId,
            'c_call_type_id' => [Call::CALL_TYPE_IN, Call::CALL_TYPE_OUT],
            'c_status_id' => [Call::STATUS_RINGING, Call::STATUS_IN_PROGRESS]
        ])
            ->innerJoinWith(['conferenceParticipants' => static function (ConferenceParticipantQuery $query) {
                $query->andOnCondition([
                    'cp_type_id' => ConferenceParticipant::TYPE_USER,
                ]);
                $query->andOnCondition(['IS NOT', 'cp_status_id', null]);
                $query->andOnCondition(['<>', 'cp_status_id', ConferenceParticipant::STATUS_LEAVE]);
            }], false)
            ->innerJoin(Conference::tableName(), 'cf_id = c_conference_id and cf_status_id = ' . Conference::STATUS_START)
            ->all();
    }

    private function addMessage($message): void
    {
        $this->messages[] = $message;
    }

    private function log(int $userId): void
    {
        foreach ($this->messages as $message) {
            if ($ntf = Notifications::create($userId, 'Complete User To User call', $message['error'], Notifications::TYPE_DANGER, true)) {
                $dataNotification = (\Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                Notifications::publish('getNewNotification', ['user_id' => $userId], $dataNotification);
            }

            \Yii::error(VarDumper::dumpAsString($message), static::class);
        }
    }
}

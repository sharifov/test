<?php

namespace src\model\conference\useCase;

use common\components\CommunicationService;
use common\models\Call;
use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use yii\helpers\VarDumper;

/**
 * Class CompleteAllJoinCalls
 *
 * @property CommunicationService $communication
 * @property array $messages
 */
class CompleteAllJoinCalls
{
    private CommunicationService $communication;
    private array $messages = [];

    public function __construct()
    {
        $this->communication = \Yii::$app->comms;
    }

    public function complete(int $userId): bool
    {
        foreach ($this->getJoinCalls($userId) as $call) {
            if ($call->c_conference_sid) {
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
    private function getJoinCalls(int $userId): array
    {
        return Call::find()->andWhere([
            'c_created_user_id' => $userId,
            'c_call_type_id' => [Call::CALL_TYPE_JOIN],
            'c_status_id' => [Call::STATUS_RINGING, Call::STATUS_IN_PROGRESS]
        ])->all();
    }

    private function addMessage($message): void
    {
        $this->messages[] = $message;
    }

    private function log(int $userId): void
    {
        foreach ($this->messages as $message) {
            if ($ntf = Notifications::create($userId, 'Complete Join call', $message['error'], Notifications::TYPE_DANGER, true)) {
                $dataNotification = (\Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                Notifications::publish('getNewNotification', ['user_id' => $userId], $dataNotification);
            }

            \Yii::error(VarDumper::dumpAsString($message), static::class);
        }
    }
}

<?php

namespace src\model\clientChatRequest\useCase\api\create\requestEventCreator;

use common\components\jobs\clientChat\ClientChatRequestCreateJob;
use src\helpers\setting\SettingHelper;
use src\model\clientChatRequest\useCase\api\create\requestEvent\ChatRequestEvent;
use src\model\clientChatRequest\useCase\api\create\requestEvent\RoomConnectedEvent;

class RoomConnectedEventCreator extends ChatRequestEventCreator
{
    public function getEvent(): ChatRequestEvent
    {
        return \Yii::createObject(RoomConnectedEvent::class);
    }

    protected function init(): void
    {
        /** @var RoomConnectedEvent $chatRequestEvent */
        $chatRequestEvent = $this->getEvent();

        $chatRequestEvent->setEventKey($this->clientChatRequest->ccr_rid)->increaseProcessCounter();

        if ($chatRequestEvent->isSameProcessStarted()) {
            $chatRequestEvent->decreaseProcessCounter();
            return;
        }

        if ($chatRequestEvent->delay > 0 || SettingHelper::isEnabledClientChatJob()) {
            $job = new ClientChatRequestCreateJob();
            $job->requestId = $this->clientChatRequest->ccr_id;
            $job->requestEventClass = $chatRequestEvent->getClassName();
            $job->delayJob = $chatRequestEvent->delay;
            if ($jobId = \Yii::$app->queue_client_chat_job->priority(10)->delay($chatRequestEvent->delay)->push($job)) {
                $this->clientChatRequest->ccr_job_id = $jobId;
                $this->clientChatRequest->save();
            } else {
                $chatRequestEvent->resetProcessCounter();
                throw new \RuntimeException('ClientChatRequest not added to queue. ClientChatRequest RID : ' .
                    $this->clientChatRequest->ccr_rid);
            }
        } else {
            $chatRequestEvent->process($this->clientChatRequest);
        }
    }
}

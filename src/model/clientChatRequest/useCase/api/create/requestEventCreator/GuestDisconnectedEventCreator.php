<?php

namespace src\model\clientChatRequest\useCase\api\create\requestEventCreator;

use common\components\jobs\clientChat\ClientChatGuestDisconnectedJob;
use common\components\jobs\clientChat\ClientChatRequestCreateJob;
use src\helpers\setting\SettingHelper;
use src\model\clientChatRequest\entity\ClientChatRequest;
use src\model\clientChatRequest\useCase\api\create\ClientChatRequestApiForm;
use src\model\clientChatRequest\useCase\api\create\requestEvent\ChatRequestEvent;
use src\model\clientChatRequest\useCase\api\create\requestEvent\GuestDisconnectedEvent;

/**
 * Class GuestDisconnectedEventCreator
 * @package src\model\clientChatRequest\useCase\api\create\requestEventCreator
 */
class GuestDisconnectedEventCreator extends ChatRequestEventCreator
{
    public function getEvent(): ChatRequestEvent
    {
        return \Yii::createObject(GuestDisconnectedEvent::class);
    }

    public function handle(ClientChatRequestApiForm $form): void
    {
        $this->clientChatRequest = ClientChatRequest::createByApi($form);
        $chatRequestEvent = $this->getEvent();
        if (SettingHelper::isEnabledClientChatJob()) {
            $job = new ClientChatGuestDisconnectedJob();
            $job->request = $this->clientChatRequest;
            $job->requestEventClass = $chatRequestEvent->getClassName();
            if (!$jobId = \Yii::$app->queue_client_chat_job->priority(10)->push($job)) {
                throw new \RuntimeException('ClientChatGuestDisconnectedJob not added to queue. ClientChatRequest RID : ' .
                    $this->clientChatRequest->ccr_rid);
            }
        } else {
            $chatRequestEvent->process($this->clientChatRequest);
        }
    }
}

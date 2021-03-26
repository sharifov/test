<?php

namespace sales\model\clientChatRequest\useCase\api\create\requestEventCreator;

use common\components\jobs\clientChat\ClientChatGuestDisconnectedJob;
use common\components\jobs\clientChat\ClientChatRequestCreateJob;
use sales\helpers\setting\SettingHelper;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\model\clientChatRequest\useCase\api\create\ClientChatRequestApiForm;
use sales\model\clientChatRequest\useCase\api\create\requestEvent\ChatRequestEvent;
use sales\model\clientChatRequest\useCase\api\create\requestEvent\GuestDisconnectedEvent;

/**
 * Class GuestDisconnectedEventCreator
 * @package sales\model\clientChatRequest\useCase\api\create\requestEventCreator
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

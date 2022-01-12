<?php

namespace src\model\clientChat\event\listener;

use common\components\jobs\clientChat\ClientChatEndConversationJob;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChat\event\ClosedStatusGroupEventInterface;
use src\model\clientChat\useCase\create\ClientChatRepository;
use src\repositories\NotFoundException;
use src\services\clientChat\ClientChatEndConversationService;
use Yii;

/**
 * Class ClientChatEndConversationListener
 * @property ClientChatRepository $clientChatRepository
 */
class ClientChatEndConversationListener
{
    private ClientChatRepository $clientChatRepository;

    /**
     * ClientChatSetStatusArchivedListener constructor.
     * @param ClientChatRepository $clientChatRepository
     */
    public function __construct(ClientChatRepository $clientChatRepository)
    {
        $this->clientChatRepository = $clientChatRepository;
    }

    public function handle(ClosedStatusGroupEventInterface $event): void
    {
        try {
            if (Yii::$app->params['settings']['enable_client_chat_job']) {
                $clientChatEndConversationJob = new ClientChatEndConversationJob();
                $clientChatEndConversationJob->clientChatId = $event->getChatId();
                $clientChatEndConversationJob->shallowClose = $event->getShallowCase();

                Yii::$app->queue_client_chat_job->priority(10)->push($clientChatEndConversationJob);
            } else {
                ClientChatEndConversationService::endConversation($event->getChatId(), $event->getShallowCase());
            }
        } catch (\Throwable $throwable) {
            \Yii::error(
                $throwable,
                'ClientChatListener:ClientChatEndConversationListener'
            );
        }
    }
}

<?php

namespace sales\model\clientChat\event;

use common\components\jobs\clientChat\ClientChatEndConversationJob;
use sales\model\clientChat\useCase\create\ClientChatRepository;
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

    public function handle(ClientChatCloseEvent $event): void
    {
        try {
            $clientChatEndConversationJob = new ClientChatEndConversationJob();
            $clientChatEndConversationJob->clientChatId = $event->clientChatId;
            $clientChatEndConversationJob->shallowClose = $event->shallowClose;

            Yii::$app->queue_client_chat_job->priority(10)->push($clientChatEndConversationJob);
        } catch (\Throwable $throwable) {
            \Yii::error(
                $throwable,
                'ClientChatListener:ClientChatEndConversationListener'
            );
        }
    }
}

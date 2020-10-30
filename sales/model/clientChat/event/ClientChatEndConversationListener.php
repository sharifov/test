<?php

namespace sales\model\clientChat\event;

use common\components\jobs\clientChat\ClientChatEndConversationJob;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\repositories\NotFoundException;
use sales\services\clientChat\ClientChatEndConversationService;
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
            if (Yii::$app->params['settings']['enable_client_chat_job']) {
                $clientChatEndConversationJob = new ClientChatEndConversationJob();
                $clientChatEndConversationJob->clientChatId = $event->clientChatId;
                $clientChatEndConversationJob->shallowClose = $event->shallowClose;

                Yii::$app->queue_client_chat_job->priority(10)->push($clientChatEndConversationJob);
            } elseif ($clientChat = ClientChatEndConversationService::endConversation($event->clientChatId, $event->shallowClose)) {
                $info = ' Id : (' . $clientChat->cch_id .
                    ') Rid : (' . $clientChat->cch_rid .
                    ') Status: (' . $clientChat->getStatusName() . ')';
                \Yii::info(
                    'Chat Bot request successfully processed. ' . PHP_EOL . $info,
                    'info\ClientChatEndConversationListener:successfully'
                );
            }
        } catch (\Throwable $throwable) {
            \Yii::error(
                $throwable,
                'ClientChatListener:ClientChatEndConversationListener'
            );
        }
    }
}

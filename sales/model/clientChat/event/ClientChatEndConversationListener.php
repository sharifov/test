<?php

namespace sales\model\clientChat\event;

use sales\model\clientChat\useCase\create\ClientChatRepository;

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

    public function handle(ClientChatSetStatusArchivedEvent $event): void
    {
        try {
            if ($clientChat = $this->clientChatRepository->findById($event->clientChatId)) {
                if (!$clientChat->ccv || !$clientChat->ccv->ccvCvd || !$clientChat->ccv->ccvCvd->cvd_visitor_rc_id) {
                    throw new \RuntimeException('Visitor RC id is not found');
                }

                $botCloseChatResult = \Yii::$app->chatBot->endConversation($clientChat->cch_rid, $clientChat->ccv->ccvCvd->cvd_visitor_rc_id, false);
                if ($botCloseChatResult['error']) {
                    throw new \RuntimeException('[Chat Bot] ' . $botCloseChatResult['error']['message'] ?? 'Unknown error message');
                }
            }
        } catch (\Throwable $throwable) {
            \Yii::error(
                $throwable,
                'ClientChatListener:ClientChatSetStatusArchivedListener'
            );
        }
    }
}

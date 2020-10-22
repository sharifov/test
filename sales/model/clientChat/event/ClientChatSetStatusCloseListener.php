<?php

namespace sales\model\clientChat\event;

use sales\model\clientChatLastMessage\ClientChatLastMessageRepository;

/**
 * Class ClientChatSetStatusCloseListener
 *
 */
class ClientChatSetStatusCloseListener
{
    /**
     * @var ClientChatLastMessageRepository
     */
    private ClientChatLastMessageRepository $clientChatLastMessageRepository;

    /**
     * ClientChatSetStatusCloseListener constructor.
     * @param ClientChatLastMessageRepository $clientChatLastMessageRepository
     */
    public function __construct(ClientChatLastMessageRepository $clientChatLastMessageRepository)
    {
        $this->clientChatLastMessageRepository = $clientChatLastMessageRepository;
    }

    public function handle(ClientChatSetStatusCloseEvent $event): void
    {
        try {
            $this->clientChatLastMessageRepository->removeByClientChat($event->clientChatId);
        } catch (\Throwable $throwable) {
            \Yii::error(
                $throwable,
                'ClientChatListener:ClientChatSetStatusCloseListener'
            );
        }
    }
}

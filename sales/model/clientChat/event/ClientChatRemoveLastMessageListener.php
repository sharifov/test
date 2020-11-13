<?php

namespace sales\model\clientChat\event;

use sales\model\clientChatLastMessage\ClientChatLastMessageRepository;

/**
 * Class ClientChatRemoveLastMessageListener
 * @property ClientChatLastMessageRepository $clientChatLastMessageRepository
 */
class ClientChatRemoveLastMessageListener
{
    /**
     * @var ClientChatLastMessageRepository
     */
    private ClientChatLastMessageRepository $clientChatLastMessageRepository;

    /**
     * ClientChatRemoveLastMessageListener constructor.
     * @param ClientChatLastMessageRepository $clientChatLastMessageRepository
     */
    public function __construct(ClientChatLastMessageRepository $clientChatLastMessageRepository)
    {
        $this->clientChatLastMessageRepository = $clientChatLastMessageRepository;
    }

    /**
     * @param $event
     */
    public function handle(ClientChatCloseEvent $event): void
    {
        try {
            $this->clientChatLastMessageRepository->removeByClientChat($event->clientChatId);
        } catch (\Throwable $throwable) {
            \Yii::error(
                $throwable,
                'ClientChatListener:ClientChatRemoveLastMessageListener'
            );
        }
    }
}

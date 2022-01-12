<?php

namespace src\model\clientChat\event\listener;

use src\model\clientChat\event\ClosedStatusGroupEventInterface;
use src\model\clientChatLastMessage\ClientChatLastMessageRepository;

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
    public function handle(ClosedStatusGroupEventInterface $event): void
    {
        try {
            $this->clientChatLastMessageRepository->removeByClientChat($event->getChatId());
        } catch (\Throwable $throwable) {
            \Yii::error(
                $throwable,
                'ClientChatListener:ClientChatRemoveLastMessageListener'
            );
        }
    }
}

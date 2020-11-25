<?php

namespace sales\model\clientChatMessage\event;

use sales\model\clientChatLastMessage\ClientChatLastMessageRepository;

/**
 * Class ClientChatMessageCreateListener
 */
class ClientChatMessageCreateListener
{
    private ClientChatLastMessageRepository $clientChatLastMessageRepository;

    /**
     * @param ClientChatLastMessageRepository $clientChatLastMessageRepository
     */
    public function __construct(ClientChatLastMessageRepository $clientChatLastMessageRepository)
    {
        $this->clientChatLastMessageRepository = $clientChatLastMessageRepository;
    }

    public function handle(ClientChatMessageCreateEvent $event): void
    {
        try {
            $this->clientChatLastMessageRepository->createOrUpdateByMessage($event->clientChatMessage);
        } catch (\Throwable $throwable) {
            \Yii::error(
                $throwable,
                'ClientChatMessageCreateListener:handle'
            );
        }
    }
}

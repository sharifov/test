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
    private ClientChatLastMessageRepository $ClientChatLastMessageRepository;

    public function __construct(ClientChatLastMessageRepository $clientChatLastMessageRepository)
	{
		$this->ClientChatLastMessageRepository = $clientChatLastMessageRepository;
	}

	public function handle(ClientChatSetStatusCloseEvent $event): void
	{
		try {
            $this->ClientChatLastMessageRepository->removeByClientChat($event->clientChatId);
		} catch (\Throwable $throwable) {
			\Yii::error($throwable,
			'ClientChatListener:ClientChatSetStatusCloseListener');
		}
	}
}
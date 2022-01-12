<?php

namespace src\model\visitorSubscription\listener;

use src\model\clientChat\entity\ClientChat;
use src\model\clientChat\useCase\create\ClientChatRepository;
use src\model\clientChatStatusLog\entity\ClientChatStatusLog;
use src\model\clientChatVisitor\entity\ClientChatVisitor;
use src\model\clientChatVisitorData\entity\ClientChatVisitorData;
use src\model\visitorSubscription\event\VisitorSubscriptionEnabled;
use src\services\clientChatService\ClientChatService;

/**
 * Class FindChatsAndRunDistributionLogic
 * @package src\model\visitorSubscription\listener
 *
 * @property ClientChatService $clientChatService
 * @property ClientChatRepository $clientChatRepository
 */
class FindChatsAndRunDistributionLogic
{
    /**
     * @var ClientChatService
     */
    private ClientChatService $clientChatService;
    /**
     * @var ClientChatRepository
     */
    private ClientChatRepository $clientChatRepository;

    public function __construct(ClientChatService $clientChatService, ClientChatRepository $clientChatRepository)
    {
        $this->clientChatService = $clientChatService;
        $this->clientChatRepository = $clientChatRepository;
    }

    public function handle(VisitorSubscriptionEnabled $event): void
    {
        $query = ClientChat::find();
        $query->innerJoin(ClientChatVisitor::tableName(), 'ccv_cch_id = cch_id');
        $query->innerJoin(ClientChatVisitorData::tableName(), 'ccv_cvd_id = cvd_id and cvd_visitor_rc_id = :rcId', [
            'rcId' => $event->visitorUid
        ]);
        $query->new();

        $chats = $query->all();

        foreach ($chats ?? [] as $chat) {
            $chat->pending(null, ClientChatStatusLog::ACTION_VISITOR_ENABLED_SUBSCRIPTION);
            $this->clientChatRepository->save($chat);
            $this->clientChatService->createUserAccessDistributionLogicJob($chat->cch_id);
        }
    }
}

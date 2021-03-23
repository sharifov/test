<?php

namespace sales\model\clientChatRequest\useCase\api\create\requestEvent;

use common\models\Notifications;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\model\clientChatStatusLog\entity\ClientChatStatusLog;
use sales\model\clientChatVisitorData\service\ChatVisitorDataService;
use sales\services\client\ClientManageService;
use sales\services\clientChatMessage\ClientChatMessageService;
use sales\services\clientChatService\ClientChatService;
use sales\services\TransactionManager;
use yii\helpers\Html;

/**
 * Class RoomConnectedEvent
 * @package sales\model\clientChatRequest\useCase\api\create\requestEvent
 *
 * @property ClientChatRepository $clientChatRepository
 * @property ClientManageService $clientManageService
 * @property ClientChatService $clientChatService
 * @property ChatVisitorDataService $chatVisitorDataService
 * @property ClientChatMessageService $clientChatMessageService
 * @property TransactionManager $transactionManager
 */
class RoomConnectedEvent implements ChatRequestEvent
{
    /**
     * @var ClientChatRepository
     */
    private ClientChatRepository $clientChatRepository;
    /**
     * @var ClientManageService
     */
    private ClientManageService $clientManageService;
    /**
     * @var ClientChatService
     */
    private ClientChatService $clientChatService;
    /**
     * @var ChatVisitorDataService
     */
    private ChatVisitorDataService $chatVisitorDataService;
    /**
     * @var ClientChatMessageService
     */
    private ClientChatMessageService $clientChatMessageService;
    /**
     * @var TransactionManager
     */
    private TransactionManager $transactionManager;

    public function __construct(
        ClientChatRepository $clientChatRepository,
        ClientManageService $clientManageService,
        ClientChatService $clientChatService,
        ChatVisitorDataService $chatVisitorDataService,
        ClientChatMessageService $clientChatMessageService,
        TransactionManager $transactionManager
    ) {
        $this->clientChatRepository = $clientChatRepository;
        $this->clientManageService = $clientManageService;
        $this->clientChatService = $clientChatService;
        $this->chatVisitorDataService = $chatVisitorDataService;
        $this->clientChatMessageService = $clientChatMessageService;
        $this->transactionManager = $transactionManager;
    }

    public function getClassName(): string
    {
        return self::class;
    }

    /**
     * @param ClientChatRequest $clientChatRequest
     * @throws \Throwable
     */
    public function process(ClientChatRequest $clientChatRequest): void
    {
        $this->transactionManager->wrap(function () use ($clientChatRequest) {
            $clientChat = $this->clientChatRepository->getOrCreateByRequest($clientChatRequest, ClientChat::SOURCE_TYPE_CLIENT);

            $clientChatCreated = $clientChat->cch_id ? false : true;

            if (!$clientChat->cch_client_id) {
                $client = $this->clientManageService->getOrCreateByClientChatRequest($clientChatRequest, (int)$clientChat->cch_project_id);
                $clientChat->cch_client_id = $client->id;
            }
            $clientChat->cch_client_online = 1;

            if (!$clientChat->cch_channel_id) {
                $channel = $this->clientChatService->assignClientChatChannel($clientChat, $clientChatRequest->getChannelIdFromData());
                if ($channel->ccc_project_id !== $clientChat->cch_project_id) {
                    throw new \DomainException('Channel project does not match project from api request');
                }

                if (!$clientChat->cch_id) {
                    $clientChat->pending(null, ClientChatStatusLog::ACTION_OPEN);
                }

                $this->clientChatRepository->save($clientChat);
                $this->clientChatService->sendRequestToUsers($clientChat);
            } else {
                if (!$clientChat->cch_id) {
                    $clientChat->pending(null, ClientChatStatusLog::ACTION_OPEN);
                }
                $this->clientChatRepository->save($clientChat);
            }

            $visitorRcId = $clientChatRequest->getClientRcId();
            $this->chatVisitorDataService->manageChatVisitorData($clientChat->cch_id, $clientChat->cch_client_id, $visitorRcId, $clientChatRequest->getDecodedData());

            if ($clientChat->cch_owner_user_id) {
                Notifications::publish('clientChatUpdateClientStatus', ['user_id' => $clientChat->cch_owner_user_id], [
                    'cchId' => $clientChat->cch_id,
                    'isOnline' => (int)$clientChat->cch_client_online,
                    'statusMessage' => Html::encode($clientChat->getClientStatusMessage()),
                ]);
            }

            if ($clientChatCreated) {
                $this->clientChatMessageService->assignMessagesToChat($clientChat);
            }
        });
    }
}

<?php

namespace src\model\clientChatRequest\useCase\api\create\requestEvent;

use common\models\Notifications;
use src\model\clientChat\componentEvent\component\ComponentDTO;
use src\model\clientChat\componentEvent\service\ComponentEventsTypeService;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChat\useCase\create\ClientChatRepository;
use src\model\clientChatCase\service\ClientChatCaseManageService;
use src\model\clientChatLead\service\ClientChatLeadMangeService;
use src\model\clientChatRequest\entity\ClientChatRequest;
use src\model\clientChatStatusLog\entity\ClientChatStatusLog;
use src\model\clientChatVisitorData\service\ChatVisitorDataService;
use src\services\client\ClientManageService;
use src\services\clientChatMessage\ClientChatMessageService;
use src\services\clientChatService\ClientChatService;
use src\services\TransactionManager;
use yii\helpers\Html;
use yii\redis\Connection;

/**
 * Class RoomConnectedEvent
 * @package src\model\clientChatRequest\useCase\api\create\requestEvent
 *
 * @property ClientChatRepository $clientChatRepository
 * @property ClientManageService $clientManageService
 * @property ClientChatService $clientChatService
 * @property ChatVisitorDataService $chatVisitorDataService
 * @property ClientChatMessageService $clientChatMessageService
 * @property TransactionManager $transactionManager
 * @property ComponentEventsTypeService $componentEventsTypeService
 * @property ClientChatLeadMangeService $clientChatLeadMangeService
 * @property ClientChatCaseManageService $clientChatCaseManageService
 * @property Connection $redis
 * @property int $delay
 * @property int $countProcesses
 * @property string $eventKey
 */
class RoomConnectedEvent implements ChatRequestEvent
{
    private const RESERVE_EVENT_KEY = 'room-id-';

    private const DELAY_EVENT_SECONDS = 1;

    private const EXPIRE_EVENT_SECONDS = 10;

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

    private ClientChatLeadMangeService $clientChatLeadMangeService;

    private ClientChatCaseManageService $clientChatCaseManageService;

    private ComponentEventsTypeService $componentEventsTypeService;

    private Connection $redis;

    public int $delay = 0;

    public int $countProcesses = 0;

    private string $eventKey = self::RESERVE_EVENT_KEY;

    public function __construct(
        ClientChatRepository $clientChatRepository,
        ClientManageService $clientManageService,
        ClientChatService $clientChatService,
        ChatVisitorDataService $chatVisitorDataService,
        ClientChatMessageService $clientChatMessageService,
        TransactionManager $transactionManager,
        ComponentEventsTypeService $componentEventsTypeService,
        ClientChatLeadMangeService $clientChatLeadMangeService,
        ClientChatCaseManageService $clientChatCaseManageService
    ) {
        $this->clientChatRepository = $clientChatRepository;
        $this->clientManageService = $clientManageService;
        $this->clientChatService = $clientChatService;
        $this->chatVisitorDataService = $chatVisitorDataService;
        $this->clientChatMessageService = $clientChatMessageService;
        $this->transactionManager = $transactionManager;
        $this->componentEventsTypeService = $componentEventsTypeService;
        $this->clientChatLeadMangeService = $clientChatLeadMangeService;
        $this->clientChatCaseManageService = $clientChatCaseManageService;
        $this->redis = \Yii::$app->redis;
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
        try {
            $clientChat = $this->clientChatRepository->getOrCreateByRequest($clientChatRequest, ClientChat::SOURCE_TYPE_CLIENT);

            $chatAlreadyCreated = $clientChat->cch_id ? true : false;

            $client = null;
            if (!$clientChat->cch_client_id) {
                $client = $this->clientManageService->detectClientFromChatRequest(
                    (int)$clientChat->cch_project_id,
                    $clientChatRequest->getClientUuId(),
                    $clientChatRequest->getEmailFromData(),
                    $clientChatRequest->getClientRcId()
                );
            }

            if (!$chatAlreadyCreated) {
                $dto = (new ComponentDTO())
                    ->setChannelId($clientChatRequest->getChannelIdFromData())
                    ->setClientChatEntity($clientChat)
                    ->setVisitorId($clientChatRequest->getClientRcId())
                    ->setClientChatRequest($clientChatRequest)
                    ->setIsChatNew(true);
                $this->componentEventsTypeService->beforeChatCreation($dto);
            }

            $clientChat->cch_client_online = 1;

            $this->transactionManager->wrap(function () use ($clientChatRequest, $clientChat, $client) {
                if (!$clientChat->cch_client_id) {
                    if ($client) {
                        $this->clientManageService->updateClientByChatRequest($client, $clientChatRequest, (int)$clientChat->cch_project_id);
                    } else {
                        $client = $this->clientManageService->createByClientChatRequest($clientChatRequest, (int)$clientChat->cch_project_id);
                    }
                    $clientChat->cch_client_id = $client->id;
                }

                if (!$clientChat->cch_channel_id) {
                    $channel = $this->clientChatService->assignClientChatChannel($clientChat, $clientChatRequest->getChannelIdFromData());
                    if ($channel->ccc_project_id !== $clientChat->cch_project_id) {
                        throw new \DomainException('Channel project does not match project from api request');
                    }

                    if (!$clientChat->cch_id) {
                        $clientChat->new(null, ClientChatStatusLog::ACTION_OPEN);
                    }

                    $this->clientChatRepository->save($clientChat);
                } else {
                    if (!$clientChat->cch_id) {
                        $clientChat->new(null, ClientChatStatusLog::ACTION_OPEN);
                    }
                    $this->clientChatRepository->save($clientChat);
                }

                $visitorRcId = $clientChatRequest->getClientRcId();
                $this->chatVisitorDataService->manageChatVisitorData($clientChat->cch_id, $clientChat->cch_client_id, $visitorRcId, $clientChatRequest->getDecodedData());
            });

            if ($clientChat->cch_owner_user_id) {
                Notifications::publish('clientChatUpdateClientStatus', ['user_id' => $clientChat->cch_owner_user_id], [
                    'cchId' => $clientChat->cch_id,
                    'isOnline' => (int)$clientChat->cch_client_online,
                    'statusMessage' => Html::encode($clientChat->getClientStatusMessage()),
                ]);
            }

            if (!$chatAlreadyCreated) {
                $this->clientChatMessageService->assignMessagesToChat($clientChat);
                \Yii::$app->snowplow->trackAction('chat', 'create', $clientChat->toArray());

                $this->componentEventsTypeService->afterChatCreation($dto);
            }
            $this->setEventKey($clientChatRequest->ccr_rid)->decreaseProcessCounter();
        } catch (\Throwable $e) {
            $this->setEventKey($clientChatRequest->ccr_rid)->decreaseProcessCounter();
            throw $e;
        }
    }

    public function setEventKey(string $key): self
    {
        $this->eventKey = self::RESERVE_EVENT_KEY . $key;
        return $this;
    }

    public function increaseProcessCounter(): void
    {
        $this->countProcesses = (int)$this->redis->get($this->eventKey);

        $this->delay = $this->countProcesses * self::DELAY_EVENT_SECONDS;
        ++$this->countProcesses;
        if ($this->countProcesses) {
            $this->redis->incr($this->eventKey);
        } else {
            $this->redis->set($this->eventKey, $this->countProcesses);
        }
        $this->redis->expire($this->eventKey, self::EXPIRE_EVENT_SECONDS);
    }

    public function isSameProcessStarted(): bool
    {
        return (int)$this->redis->get($this->eventKey) > 1;
    }

    public function resetProcessCounter(): void
    {
        $this->redis->del($this->eventKey);
    }

    public function decreaseProcessCounter(): void
    {
        $countProcesses = (int)$this->redis->get($this->eventKey);
        if ($countProcesses > 0) {
            $this->redis->decr($this->eventKey);
        } else {
            $this->resetProcessCounter();
        }
    }
}

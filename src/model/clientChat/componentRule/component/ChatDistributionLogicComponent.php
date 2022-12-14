<?php

namespace src\model\clientChat\componentRule\component;

use frontend\helpers\JsonHelper;
use src\model\clientChat\componentEvent\component\ComponentDTOInterface;
use src\model\clientChat\useCase\create\ClientChatRepository;
use src\model\clientChatStatusLog\entity\ClientChatStatusLog;
use src\services\clientChatService\ClientChatService;
use Yii;

/**
 * Class ChatDistributionLogicComponent
 * @package src\model\clientChat\componentRule\component
 *
 * @property-read ClientChatService $clientChatService
 * @property-read ClientChatRepository $clientChatRepository
 */
class ChatDistributionLogicComponent implements RunnableComponentInterface
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

    public function run(ComponentDTOInterface $dto): void
    {
        if ($dto->getIsChatNew() && $clientChat = $dto->getClientChatEntity()) {
            $clientChat->pending(null, ClientChatStatusLog::ACTION_PENDING_BY_DISTRIBUTION_LOGIC);
            $this->clientChatRepository->save($clientChat);
            $this->clientChatService->sendRequestToUsers($clientChat);
        }
    }

    public function getDefaultConfig(): array
    {
        return [];
    }

    public function getDefaultConfigJson(): string
    {
        return JsonHelper::encode($this->getDefaultConfig());
    }
}

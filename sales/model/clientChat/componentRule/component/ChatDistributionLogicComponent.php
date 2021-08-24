<?php

namespace sales\model\clientChat\componentRule\component;

use frontend\helpers\JsonHelper;
use sales\model\clientChat\componentEvent\component\ComponentDTOInterface;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatStatusLog\entity\ClientChatStatusLog;
use sales\services\clientChatService\ClientChatService;
use Yii;

/**
 * Class ChatDistributionLogicComponent
 * @package sales\model\clientChat\componentRule\component
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
//        Yii::info('ChatDistributionLogicComponent isNew: ' . $dto->getIsChatNew(), 'info\ChatDistributionLogicComponent');
        if ($dto->getIsChatNew()) {
//            Yii::info('ChatDistributionLogic started', 'info\ChatDistributionLogicComponent');
            $dto->getClientChatEntity()->pending(null, ClientChatStatusLog::ACTION_PENDING_BY_DISTRIBUTION_LOGIC);
            $this->clientChatRepository->save($dto->getClientChatEntity());
            $this->clientChatService->sendRequestToUsers($dto->getClientChatEntity());
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

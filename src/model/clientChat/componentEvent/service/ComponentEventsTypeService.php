<?php

namespace src\model\clientChat\componentEvent\service;

use src\helpers\app\AppHelper;
use src\model\clientChat\componentEvent\component\ComponentDTOInterface;
use src\model\clientChat\componentEvent\entity\ClientChatComponentEvent;
use src\model\clientChat\componentEvent\entity\ClientChatComponentEventQuery;
use src\model\clientChat\componentRule\entity\RunnableComponent;
use src\model\clientChat\componentRule\service\RunnableComponentService;
use yii\helpers\ArrayHelper;

/**
 * Class ComponentEventsTypeService
 * @package src\model\clientChat\componentEvent\service
 *
 * @property-read RunnableComponentService $runnableComponentService
 */
class ComponentEventsTypeService
{
    private const AFTER_CHAT_CREATION_DEFAULT_RUNNABLE_COMPONENTS = [
        RunnableComponent::CHAT_DISTRIBUTION_LOGIC
    ];

    /**
     * @var RunnableComponentService
     */
    private RunnableComponentService $runnableComponentService;

    public function __construct(RunnableComponentService $runnableComponentService)
    {
        $this->runnableComponentService = $runnableComponentService;
    }

    public function beforeChatCreation(ComponentDTOInterface $dto): void
    {
        $componentEvents = ClientChatComponentEventQuery::findByChannelIdBeforeChatCreation($dto->getChannelId());
        $this->executeComponentEvents($componentEvents, $dto);
    }

    public function afterChatCreation(ComponentDTOInterface $dto): void
    {
        $componentEvents = ClientChatComponentEventQuery::findByChannelIdAfterChatCreation($dto->getChannelId());
        if ($componentEvents) {
            $this->executeComponentEvents($componentEvents, $dto);
        } else {
            $this->runnableComponentService->executeDefaultRunnableComponents(self::AFTER_CHAT_CREATION_DEFAULT_RUNNABLE_COMPONENTS, $dto);
        }
    }

    /**
     * @param ClientChatComponentEvent[] $componentEvents
     * @param ComponentDTOInterface $dto
     */
    private function executeComponentEvents(array $componentEvents, ComponentDTOInterface $dto): void
    {
        foreach ($componentEvents as $componentEvent) {
            try {
                $dto->setComponentEventConfig((string)$componentEvent->ccce_component_config);
                $result = $componentEvent->getComponentClassObject()->run($dto);
                $this->runnableComponentService->executeRunnableComponents($result, $componentEvent->ccce_id, $dto);
            } catch (\RuntimeException $e) {
                \Yii::warning([
                    'message' => $e->getMessage(),
                    'chatId' => $dto->getClientChatEntity()->cch_id ?? null,
                    'channelId' => $dto->getClientChatEntity()->cch_channel_id ?? null,
                    'chatRoomId' => $dto->getClientChatEntity()->cch_rid ?? null,
                    'clientId' => $dto->getClientChatEntity()->cch_client_id ?? null,
                    'trace' => $e->getTrace()
                ], 'ComponentEventsTypeService::executeComponentEvents::RuntimeException');
                $this->runnableComponentService->executeRunnableComponents('null', $componentEvent->ccce_id, $dto);
            } catch (\Throwable $e) {
                \Yii::error([
                    'message' => $e->getMessage(),
                    'chatId' => $dto->getClientChatEntity()->cch_id ?? null,
                    'channelId' => $dto->getClientChatEntity()->cch_channel_id ?? null,
                    'chatRoomId' => $dto->getClientChatEntity()->cch_rid ?? null,
                    'clientId' => $dto->getClientChatEntity()->cch_client_id ?? null,
                    'trace' => $e->getTrace()
                ], 'ComponentEventsTypeService::executeComponentEvents::Throwable');
            }
        }
    }
}

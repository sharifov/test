<?php

namespace sales\model\clientChat\componentEvent\service;

use sales\helpers\app\AppHelper;
use sales\model\clientChat\componentEvent\component\ComponentDTOInterface;
use sales\model\clientChat\componentEvent\entity\ClientChatComponentEvent;
use sales\model\clientChat\componentEvent\entity\ClientChatComponentEventQuery;
use sales\model\clientChat\componentRule\entity\RunnableComponent;
use sales\model\clientChat\componentRule\service\RunnableComponentService;
use yii\helpers\ArrayHelper;

/**
 * Class ComponentEventsTypeService
 * @package sales\model\clientChat\componentEvent\service
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
                \Yii::error(AppHelper::throwableLog($e, true), 'ComponentEventsTypeService::executeComponentEvents::RuntimeException');
                $this->runnableComponentService->executeRunnableComponents('null', $componentEvent->ccce_id, $dto);
            } catch (\Throwable $e) {
                \Yii::error(AppHelper::throwableLog($e, true), 'ComponentEventsTypeService::executeComponentEvents::Throwable');
            }
        }
    }
}

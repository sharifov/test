<?php

namespace sales\model\clientChat\componentRule\service;

use sales\helpers\app\AppHelper;
use sales\model\clientChat\componentEvent\component\ComponentDTOInterface;
use sales\model\clientChat\componentRule\entity\ClientChatComponentRule;
use sales\model\clientChat\componentRule\entity\ClientChatComponentRuleQuery;

class RunnableComponentService
{
    public function executeRunnableComponents(string $value, int $componentEventId, ComponentDTOInterface $dto): void
    {
        $runnableComponents = ClientChatComponentRuleQuery::findByValueAndComponentEventId($value, $componentEventId);

        foreach ($runnableComponents as $runnableComponent) {
            $dto->setRunnableComponentConfig($runnableComponent->cccr_component_config);
            $runnableComponent->getClassObject()->run($dto);
        }
    }

    /**
     * @param int[] $defaultComponents
     * @param ComponentDTOInterface $dto
     */
    public function executeDefaultRunnableComponents(array $defaultComponents, ComponentDTOInterface $dto): void
    {
        foreach ($defaultComponents as $defaultComponent) {
            try {
                $rule = new ClientChatComponentRule();
                $rule->cccr_runnable_component = $defaultComponent;
                $rule->getClassObject()->run($dto);
            } catch (\Throwable $e) {
                \Yii::error(AppHelper::throwableLog($e, true), 'RunnableComponentService::executeDefaultRunnableComponents::Throwable');
            }
        }
    }
}

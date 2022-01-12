<?php

namespace src\model\clientChat\componentRule\service;

use src\helpers\app\AppHelper;
use src\model\clientChat\componentEvent\component\ComponentDTOInterface;
use src\model\clientChat\componentRule\entity\ClientChatComponentRule;
use src\model\clientChat\componentRule\entity\ClientChatComponentRuleQuery;

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

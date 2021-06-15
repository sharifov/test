<?php

namespace sales\model\clientChat\componentRule\service;

use sales\model\clientChat\componentRule\entity\ClientChatComponentRuleQuery;

class RunnableComponentService
{
    public function executeRunnableComponents(string $value, int $componentEventId)
    {
        $runnableComponents = ClientChatComponentRuleQuery::findByValueAndComponentEventId($value, $componentEventId);

        foreach ($runnableComponents as $runnableComponent) {
            $runnableComponent->getClassObject()->run();
        }
    }
}

<?php

namespace src\model\clientChat\componentRule\repository;

use src\model\clientChat\componentRule\entity\ClientChatComponentRule;

class ClientChatComponentRuleRepository
{
    public function save(ClientChatComponentRule $componentRule): ClientChatComponentRule
    {
        if ($componentRule->save()) {
            return $componentRule;
        }
        throw new \RuntimeException($componentRule->getErrorSummary(true)[0]);
    }
}

<?php

namespace src\model\clientChat\componentRule\entity;

class ClientChatComponentRuleQuery
{
    /**
     * @param string $value
     * @param int $componentEventId
     * @return array|ClientChatComponentRule[]
     */
    public static function findByValueAndComponentEventId(string $value, int $componentEventId): array
    {
        return ClientChatComponentRule::find()
            ->byValue($value)
            ->byComponentEventId($componentEventId)
            ->enabled()
            ->orderBy(['cccr_sort_order' => SORT_ASC])->all();
    }

    public static function deleteByComponentEventId(int $id): int
    {
        return ClientChatComponentRule::deleteAll(['cccr_component_event_id' => $id]);
    }
}

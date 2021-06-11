<?php

namespace sales\model\clientChat\componentEvent\entity;

class ClientChatComponentEventQuery
{
    /**
     * @param int $id
     * @return ClientChatComponentEvent[]
     */
    public static function findByChannelIdBeforeChatCreation(int $id): array
    {
        return ClientChatComponentEvent::find()
            ->byChannelId($id)
            ->enabled()
            ->beforeChatCreation()
            ->orderBy(['ccce_sort_order' => SORT_ASC])->all();
    }

    public static function findByChannelIdAfterChatCreation(int $id): array
    {
        return ClientChatComponentEvent::find()
            ->byChannelId($id)
            ->enabled()
            ->afterChatCreation()
            ->orderBy(['ccce_sort_order' => SORT_ASC])->all();
    }
}

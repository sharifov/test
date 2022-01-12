<?php

namespace src\model\clientChat\componentEvent\entity;

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
            ->orChannelIdIsNotSet()
            ->enabled()
            ->beforeChatCreation()
            ->orderBy(['ccce_sort_order' => SORT_ASC])->all();
    }

    public static function findByChannelIdAfterChatCreation(int $id): array
    {
        return ClientChatComponentEvent::find()
            ->byChannelId($id)
            ->orChannelIdIsNotSet()
            ->enabled()
            ->afterChatCreation()
            ->orderBy(['ccce_sort_order' => SORT_ASC])->all();
    }
}

<?php

namespace sales\model\clientChat\componentEvent\component;

use sales\model\clientChat\entity\ClientChat;

/**
 * Class ComponentDTO
 * @package sales\model\clientChat\componentEvent\component
 *
 * @property-read ClientChat $chat
 * @property-read int $channelId
 * @property-read string $componentConfig
 */
class ComponentDTO implements ComponentDTOInterface
{
    private ClientChat $chat;

    private int $channelId;

    private string $componentConfig;

    public function setClientChatEntity(ClientChat $chat): ComponentDTOInterface
    {
        $this->chat = $chat;
        return $this;
    }

    public function setChannelId(int $id): ComponentDTOInterface
    {
        $this->channelId = $id;
        return $this;
    }

    public function setComponentEventConfig(string $config): ComponentDTOInterface
    {
        $this->componentConfig = $config;
        return $this;
    }

    public function getClientChatEntity(): ClientChat
    {
        return $this->chat;
    }

    public function getChannelId(): int
    {
        return $this->channelId;
    }

    public function getComponentEventConfig(): string
    {
        return $this->componentConfig;
    }
}

<?php

namespace sales\model\clientChat\componentEvent\component;

use sales\model\clientChat\entity\ClientChat;

interface ComponentDTOInterface
{
    public function setClientChatEntity(ClientChat $chat): self;

    public function setChannelId(int $id): self;

    public function setComponentEventConfig(string $config): self;

    public function getClientChatEntity(): ?ClientChat;

    public function getChannelId(): ?int;

    public function getComponentEventConfig();
}

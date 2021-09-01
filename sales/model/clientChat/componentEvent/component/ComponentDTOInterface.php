<?php

namespace sales\model\clientChat\componentEvent\component;

use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatRequest\entity\ClientChatRequest;

interface ComponentDTOInterface
{
    public function setClientChatEntity(ClientChat $chat): self;

    public function setChannelId(int $id): self;

    public function setComponentEventConfig(string $config): self;

    public function setVisitorId(string $id): self;

    public function getClientChatEntity(): ?ClientChat;

    public function getChannelId(): ?int;

    public function getComponentEventConfig();

    public function getVisitorId(): ?string;

    public function setIsChatNew(bool $value): self;

    public function getIsChatNew(): bool;

    public function setRunnableComponentConfig(string $config): self;

    public function getRunnableComponentConfig(): array;

    public function setClientChatRequest(ClientChatRequest $request): self;

    public function getClientChatRequest(): ?ClientChatRequest;
}

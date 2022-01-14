<?php

namespace src\model\clientChat\componentEvent\component;

use frontend\helpers\JsonHelper;
use src\model\clientChat\entity\ClientChat;
use src\model\clientChatRequest\entity\ClientChatRequest;
use yii\helpers\Json;

/**
 * Class ComponentDTO
 * @package src\model\clientChat\componentEvent\component
 *
 * @property-read ClientChat $chat
 * @property-read int $channelId
 * @property-read string $componentConfig
 * @property-read string|null $visitorId
 * @property-read bool $isChatNew
 * @property-read array $runnableComponentConfig
 * @property-read ClientChatRequest|null $clientChatRequest
 */
class ComponentDTO implements ComponentDTOInterface
{
    private ClientChat $chat;

    private ?ClientChatRequest $clientChatRequest;

    private int $channelId;

    private string $componentConfig;

    private string $visitorId;

    private bool $isChatNew;

    private array $runnableComponentConfig;

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

    public function getClientChatEntity(): ?ClientChat
    {
        return $this->chat;
    }

    public function getChannelId(): ?int
    {
        return $this->channelId;
    }

    public function getComponentEventConfig(): string
    {
        return $this->componentConfig;
    }

    public function setVisitorId(string $id): ComponentDTOInterface
    {
        $this->visitorId = $id;
        return $this;
    }

    public function getVisitorId(): ?string
    {
        return $this->visitorId;
    }

    public function setIsChatNew(bool $value): ComponentDTOInterface
    {
        $this->isChatNew = $value;
        return $this;
    }

    public function getIsChatNew(): bool
    {
        return $this->isChatNew;
    }

    public function setRunnableComponentConfig(string $config): ComponentDTOInterface
    {
        $this->runnableComponentConfig = JsonHelper::decode($config);
        return $this;
    }

    public function getRunnableComponentConfig(): array
    {
        return $this->runnableComponentConfig;
    }

    public function setClientChatRequest(ClientChatRequest $request): self
    {
        $this->clientChatRequest = $request;
        return $this;
    }

    public function getClientChatRequest(): ?ClientChatRequest
    {
        return $this->clientChatRequest;
    }
}

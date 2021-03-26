<?php

namespace modules\order\src\entities\order\events;

/**
 * Class OrderDeclinedEvent
 *
 * @property int $orderId
 * @property int|null $startStatus
 * @property int $endStatus
 * @property string|null $description
 * @property int|null $actionId
 * @property int|null $ownerId
 * @property int|null $creatorId
 */
class OrderDeclinedEvent implements OrderChangeStatusInterface
{
    public int $orderId;
    public ?int $startStatus;
    public int $endStatus;
    public ?string $description;
    public ?int $actionId;
    public ?int $ownerId;
    public ?int $creatorId;

    public function __construct(
        int $orderId,
        ?int $startStatus,
        int $endStatus,
        ?string $description,
        ?int $actionId,
        ?int $ownerId,
        ?int $creatorId
    ) {
        $this->orderId = $orderId;
        $this->startStatus = $startStatus;
        $this->endStatus = $endStatus;
        $this->description = $description;
        $this->actionId = $actionId;
        $this->ownerId = $ownerId;
        $this->creatorId = $creatorId;
    }

    public function getId(): int
    {
        return $this->orderId;
    }

    public function getStartStatus(): ?int
    {
        return $this->startStatus;
    }

    public function getEndStatus(): int
    {
        return $this->endStatus;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getActionId(): ?int
    {
        return $this->actionId;
    }

    public function getOwnerId(): ?int
    {
        return $this->ownerId;
    }

    public function getCreatorId(): ?int
    {
        return $this->creatorId;
    }
}

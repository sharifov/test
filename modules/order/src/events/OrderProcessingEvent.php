<?php

namespace modules\order\src\events;

use modules\order\src\entities\order\events\OrderChangeStatusInterface;
use modules\order\src\entities\order\Order;
use yii\base\Component;

/**
 * Class OrderProcessingEvent
 *
 * @property Order $order
 * @property int|null $startStatus
 * @property int $endStatus
 * @property string|null $description
 * @property int|null $actionId
 * @property int|null $ownerId
 * @property int|null $creatorId
 */
class OrderProcessingEvent extends Component implements OrderChangeStatusInterface
{
    public $order;
    public ?int $startStatus;
    public int $endStatus;
    public ?string $description;
    public ?int $actionId;
    public ?int $ownerId;
    public ?int $creatorId;

    public function __construct(
        Order $order,
        ?int $startStatus,
        int $endStatus,
        ?string $description,
        ?int $actionId,
        ?int $ownerId,
        ?int $creatorId,
        $config = []
    ) {
        parent::__construct($config);
        $this->order = $order;
        $this->startStatus = $startStatus;
        $this->endStatus = $endStatus;
        $this->description = $description;
        $this->actionId = $actionId;
        $this->ownerId = $ownerId;
        $this->creatorId = $creatorId;
    }

    public function getId(): int
    {
        return $this->order->or_id;
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

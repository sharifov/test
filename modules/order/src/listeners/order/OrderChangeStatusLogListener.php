<?php

namespace modules\order\src\listeners\order;

use modules\order\src\entities\order\events\OrderChangeStatusInterface;
use modules\order\src\entities\orderStatusLog\CreateDto;
use modules\order\src\services\OrderStatusLogService;

class OrderChangeStatusLogListener
{
    private $logger;

    public function __construct(OrderStatusLogService $logger)
    {
        $this->logger = $logger;
    }

    public function handle(OrderChangeStatusInterface $event): void
    {
        try {
            $this->logger->log(
                new CreateDto(
                    $event->getId(),
                    $event->getStartStatus(),
                    $event->getEndStatus(),
                    $event->getDescription(),
                    $event->getActionId(),
                    $event->getOwnerId(),
                    $event->getCreatorId()
                )
            );
        } catch (\Throwable $e) {
            \Yii::error($e, 'Listeners:OrderChangeStatusLogListener');
        }
    }
}

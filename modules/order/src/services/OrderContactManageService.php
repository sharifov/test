<?php

namespace modules\order\src\services;

use modules\order\src\entities\orderContact\OrderContact;
use modules\order\src\entities\orderContact\OrderContactRepository;
use modules\order\src\jobs\OrderCreateClientByOrderContactJob;
use modules\order\src\processManager\queue\Queue;

/**
 * Class OrderContactManageService
 * @package modules\order\src\services
 *
 * @property OrderContactRepository $orderContactRepository
 * @property Queue $queue
 */
class OrderContactManageService
{
    private OrderContactRepository $orderContactRepository;
    private Queue $queue;

    public function __construct(OrderContactRepository $orderContactRepository, Queue $queue)
    {
        $this->orderContactRepository = $orderContactRepository;
        $this->queue = $queue;
    }

    public function create(
        int $orderId,
        string $firstName,
        ?string $lastName,
        ?string $middleName,
        string $email,
        string $phoneNumber,
        int $projectId
    ): OrderContact {
        $orderContact = OrderContact::create(
            $orderId,
            $firstName,
            $lastName,
            $middleName,
            $email,
            $phoneNumber
        );
        $this->orderContactRepository->save($orderContact);
        $this->queue->push(new OrderCreateClientByOrderContactJob($orderContact->oc_id, $projectId));
        return $orderContact;
    }
}

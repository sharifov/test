<?php

namespace modules\order\src\payment;

use common\models\Payment;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

/**
 * Class PaymentRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class PaymentRepository
{
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function find(int $id): Payment
    {
        if ($payment = Payment::findOne($id)) {
            return $payment;
        }
        throw new NotFoundException('Payment is not found');
    }

    public function save(Payment $payment): void
    {
        if (!$payment->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        $this->eventDispatcher->dispatchAll($payment->releaseEvents());
    }

    public function remove(Payment $payment): void
    {
        if (!$payment->delete()) {
            throw new \RuntimeException('Removing error');
        }
        $this->eventDispatcher->dispatchAll($payment->releaseEvents());
    }
}

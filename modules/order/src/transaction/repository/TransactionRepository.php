<?php

namespace modules\order\src\transaction\repository;

use common\models\Transaction;
use src\dispatchers\EventDispatcher;

/**
 * Class TransactionRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class TransactionRepository
{
    private EventDispatcher $eventDispatcher;

    /**
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function save(Transaction $transaction): void
    {
        if (!$transaction->save(false)) {
            throw new \RuntimeException('Transaction save is fail');
        }
        $this->eventDispatcher->dispatchAll($transaction->releaseEvents());
    }

    public function remove(Transaction $transaction): void
    {
        if (!$transaction->delete()) {
            throw new \RuntimeException('Transaction remove is fail');
        }
    }
}

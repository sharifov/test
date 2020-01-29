<?php

namespace modules\order\src\entities\orderStatusLog;

use modules\order\src\exceptions\OrderCodeException;

/**
 * Class OrderStatusLogRepository
 */
class OrderStatusLogRepository
{
    public function getPrevious(int $offerId): ?OrderStatusLog
    {
        if ($log = OrderStatusLog::find()->last($offerId)->one()) {
            return $log;
        }
        return null;
    }

    public function save(OrderStatusLog $log): int
    {
        if (!$log->save(false)) {
            throw new \RuntimeException('Saving error', OrderCodeException::ORDER_STATUS_LOG_SAVE);
        }
        return $log->orsl_id;
    }

    public function remove(OrderStatusLog $log): void
    {
        if (!$log->delete()) {
            throw new \RuntimeException('Removing error', OrderCodeException::ORDER_STATUS_LOG_REMOVE);
        }
    }
}

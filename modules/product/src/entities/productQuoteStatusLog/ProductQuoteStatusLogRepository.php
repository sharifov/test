<?php

namespace modules\product\src\entities\productQuoteStatusLog;

use modules\product\src\exceptions\ProductCodeException;

/**
 * Class ProductQuoteStatusLogRepository
 */
class ProductQuoteStatusLogRepository
{
    public function getPrevious(int $productQuoteId): ?ProductQuoteStatusLog
    {
        if ($log = ProductQuoteStatusLog::find()->last($productQuoteId)->one()) {
            return $log;
        }
        return null;
    }

    public function save(ProductQuoteStatusLog $log): int
    {
        if (!$log->save(false)) {
            throw new \RuntimeException('Saving error', ProductCodeException::PRODUCT_QUOTE_STATUS_LOG_SAVE);
        }
        return $log->pqsl_id;
    }

    public function remove(ProductQuoteStatusLog $log): void
    {
        if (!$log->delete()) {
            throw new \RuntimeException('Removing error', ProductCodeException::PRODUCT_QUOTE_STATUS_LOG_REMOVE);
        }
    }
}

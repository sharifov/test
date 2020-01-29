<?php

namespace modules\offer\src\entities\offerStatusLog;

use modules\offer\src\exceptions\OfferCodeException;

/**
 * Class OfferStatusLogRepository
 */
class OfferStatusLogRepository
{
    public function getPrevious(int $offerId): ?OfferStatusLog
    {
        if ($log = OfferStatusLog::find()->last($offerId)->one()) {
            return $log;
        }
        return null;
    }

    public function save(OfferStatusLog $log): int
    {
        if (!$log->save(false)) {
            throw new \RuntimeException('Saving error', OfferCodeException::OFFER_STATUS_LOG_SAVE);
        }
        return $log->osl_id;
    }

    public function remove(OfferStatusLog $log): void
    {
        if (!$log->delete()) {
            throw new \RuntimeException('Removing error', OfferCodeException::OFFER_STATUS_LOG_REMOVE);
        }
    }
}

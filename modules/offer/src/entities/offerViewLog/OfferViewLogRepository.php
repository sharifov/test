<?php

namespace modules\offer\src\entities\offerViewLog;

use modules\offer\src\exceptions\OfferCodeException;

class OfferViewLogRepository
{
    public function save(OfferViewLog $log): int
    {
        if (!$log->save(false)) {
            throw new \RuntimeException('Saving error', OfferCodeException::OFFER_VIEW_LOG_SAVE);
        }
        return $log->ofvwl_id;
    }

    public function remove(OfferViewLog $log): void
    {
        if (!$log->delete()) {
            throw new \RuntimeException('Removing error', OfferCodeException::OFFER_VIEW_LOG_REMOVE);
        }
    }
}

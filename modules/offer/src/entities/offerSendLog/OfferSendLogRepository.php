<?php

namespace modules\offer\src\entities\offerSendLog;

use modules\offer\src\exceptions\OfferCodeException;

class OfferSendLogRepository
{
    public function save(OfferSendLog $log): int
    {
        if (!$log->save(false)) {
            throw new \RuntimeException('Saving error', OfferCodeException::OFFER_SEND_LOG_SAVE);
        }
        return $log->ofsndl_id;
    }

    public function remove(OfferSendLog $log): void
    {
        if (!$log->delete()) {
            throw new \RuntimeException('Removing error', OfferCodeException::OFFER_SEND_LOG_REMOVE);
        }
    }
}

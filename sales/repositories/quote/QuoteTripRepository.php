<?php

namespace sales\repositories\quote;

use common\models\QuoteTrip;

/**
 * Class QuoteTripRepository
 */
class QuoteTripRepository
{

    /**
     * @param QuoteTrip $quoteTrip
     * @return int
     */
    public function save(QuoteTrip $quoteTrip): int
    {
        if (!$quoteTrip->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        return $quoteTrip->qt_id;
    }

}

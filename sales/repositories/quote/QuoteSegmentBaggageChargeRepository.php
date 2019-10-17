<?php

namespace sales\repositories\quote;

use common\models\QuoteSegmentBaggageCharge;

/**
 * Class QuoteSegmentBaggageChargeRepository
 */
class QuoteSegmentBaggageChargeRepository
{

    /**
     * @param QuoteSegmentBaggageCharge $quoteSegmentBaggageCharge
     * @return int
     */
    public function save(QuoteSegmentBaggageCharge $quoteSegmentBaggageCharge): int
    {
        if (!$quoteSegmentBaggageCharge->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        return $quoteSegmentBaggageCharge->qsbc_id;
    }

}

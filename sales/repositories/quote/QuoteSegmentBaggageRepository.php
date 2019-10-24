<?php

namespace sales\repositories\quote;

use common\models\QuoteSegmentBaggage;

/**
 * Class QuoteSegmentBaggageRepository
 */
class QuoteSegmentBaggageRepository
{

    /**
     * @param QuoteSegmentBaggage $quoteSegmentBaggage
     * @return int
     */
    public function save(QuoteSegmentBaggage $quoteSegmentBaggage): int
    {
        if (!$quoteSegmentBaggage->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        return $quoteSegmentBaggage->qsb_id;
    }

}

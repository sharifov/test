<?php

namespace sales\repositories\quote;

use common\models\QuoteSegmentStop;

/**
 * Class QuoteSegmentStopRepository
 */
class QuoteSegmentStopRepository
{

    /**
     * @param QuoteSegmentStop $quoteSegmentStop
     * @return int
     */
    public function save(QuoteSegmentStop $quoteSegmentStop): int
    {
        if (!$quoteSegmentStop->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        return $quoteSegmentStop->qss_id;
    }

}

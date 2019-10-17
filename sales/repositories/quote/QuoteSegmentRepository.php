<?php

namespace sales\repositories\quote;

use common\models\QuoteSegment;

/**
 * Class QuoteSegmentRepository
 */
class QuoteSegmentRepository
{

    /**
     * @param QuoteSegment $quoteSegment
     * @return int
     */
    public function save(QuoteSegment $quoteSegment): int
    {
        if (!$quoteSegment->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        return $quoteSegment->qs_id;
    }

}

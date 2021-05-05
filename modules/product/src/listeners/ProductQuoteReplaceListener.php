<?php

namespace modules\product\src\listeners;

use modules\product\src\entities\productQuote\events\ProductQuoteReplaceEvent;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use modules\product\src\repositories\ProductQuoteRelationRepository;
use Yii;

/**
 * Class ProductQuoteReplaceListener
 */
class ProductQuoteReplaceListener
{
    public function handle(ProductQuoteReplaceEvent $event): void
    {
        try {
            (new ProductQuoteRelationRepository())->save(
                ProductQuoteRelation::replace(
                    $event->originProductQuoteId,
                    $event->productQuote->pq_id
                )
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:ProductQuoteReplaceListener');
        }
    }
}

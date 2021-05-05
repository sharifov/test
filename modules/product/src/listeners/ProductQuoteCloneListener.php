<?php

namespace modules\product\src\listeners;

use modules\product\src\entities\productQuote\events\ProductQuoteCloneCreatedEvent;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use modules\product\src\repositories\ProductQuoteRelationRepository;
use Yii;

/**
 * Class ProductQuoteCloneListener
 */
class ProductQuoteCloneListener
{
    public function handle(ProductQuoteCloneCreatedEvent $event): void
    {
        try {
            (new ProductQuoteRelationRepository())->save(
                ProductQuoteRelation::clone(
                    $event->quote->pq_clone_id,
                    $event->getId(),
                    $event->getCreatorId()
                )
            );
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:ProductQuoteCloneListener');
        }
    }
}

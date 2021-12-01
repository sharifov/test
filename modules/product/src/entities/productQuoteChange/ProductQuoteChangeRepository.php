<?php

namespace modules\product\src\entities\productQuoteChange;

use modules\product\src\entities\productQuote\ProductQuote;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

/**
 * Class ProductQuoteChangeRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class ProductQuoteChangeRepository
{
    private EventDispatcher $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function find(int $id): ProductQuoteChange
    {
        if ($productQuote = ProductQuoteChange::find()->byId($id)->one()) {
            return $productQuote;
        }
        throw new NotFoundException('Product Quote Change is not found.');
    }

    public function findByProductQuoteId(int $id): ProductQuoteChange
    {
        if ($productQuote = ProductQuoteChange::find()->byProductQuote($id)->orderBy(['pqc_id' => SORT_DESC])->one()) {
            return $productQuote;
        }
        throw new NotFoundException('Product Quote Change is not found.');
    }

    public function findParentRelated(ProductQuote $productQuote): ProductQuoteChange
    {
        $relatedParent = $productQuote->relateParent;
        if (!$relatedParent) {
            throw new \DomainException('No found related parent quote.');
        }
        return $this->findByProductQuoteId($relatedParent->pq_id);
    }

    public function save(ProductQuoteChange $change): void
    {
        if (!$change->save(false)) {
            throw new \RuntimeException('Saving error.');
        }
        $this->eventDispatcher->dispatchAll($change->releaseEvents());
    }
}

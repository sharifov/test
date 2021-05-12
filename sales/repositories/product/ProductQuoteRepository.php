<?php

namespace sales\repositories\product;

use modules\product\src\entities\productQuote\ProductQuote;
use sales\dispatchers\EventDispatcher;
use yii\web\NotFoundHttpException;

/**
 * Class ProductQuoteRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class ProductQuoteRepository
{
    private $eventDispatcher;

    /**
     * CasesRepository constructor.
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }
    /**
     * @param ProductQuote $productQuote
     * @return int
     */
    public function save(ProductQuote $productQuote): int
    {
        if (!$productQuote->save()) {
            throw new \RuntimeException($productQuote->getErrorSummary(false)[0]);
        }
        $this->eventDispatcher->dispatchAll($productQuote->releaseEvents());
        return $productQuote->pq_id;
    }

    public function find(int $productQuoteId): ProductQuote
    {
        if ($productQuote = ProductQuote::findOne($productQuoteId)) {
            return $productQuote;
        }
        throw new NotFoundHttpException('Product Quote not found');
    }
}

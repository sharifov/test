<?php

namespace modules\product\src\entities\productQuoteChange;

use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuoteFlight;
use modules\product\src\entities\productQuote\ProductQuote;
use src\dispatchers\EventDispatcher;
use src\repositories\NotFoundException;

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

    public function findByProductIdAndType(int $id, int $typeId): ProductQuoteChange
    {
        $productQuote = ProductQuoteChange::find()
            ->byProductQuote($id)
            ->byType($typeId)
            ->orderBy(['pqc_id' => SORT_DESC])
            ->one();

        if (!$productQuote) {
            throw new NotFoundException('Product Quote Change is not found.');
        }
        return $productQuote;
    }

    public function findByProductQuoteId(int $id): ProductQuoteChange
    {
        if ($productQuote = ProductQuoteChange::find()->byProductQuote($id)->orderBy(['pqc_id' => SORT_DESC])->one()) {
            return $productQuote;
        }
        throw new NotFoundException('Product Quote Change is not found.');
    }

    public function findParentRelated(ProductQuote $productQuote, ?int $typeId = null): ProductQuoteChange
    {
        $relatedParent = $productQuote->relateParent;
        if (!$relatedParent) {
            throw new \DomainException('No found related parent quote.');
        }
        if ($typeId) {
            return $this->findByProductIdAndType($relatedParent->pq_id, $typeId);
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

    /**
     * @param string $bookingId
     * @param array $statuses
     * @return ProductQuoteChange[]|array
     */
    public function findAllByBookingId(string $bookingId, array $statuses): array
    {
        return ProductQuoteChange::find()
            ->innerJoin(FlightQuote::tableName(), 'fq_product_quote_id = pqc_pq_id')
            ->innerJoin(FlightQuoteFlight::tableName(), 'fqf_fq_id = fq_id')
            ->where(['fqf_booking_id' => $bookingId])
            ->byStatuses($statuses)
            ->orderBy(['pqc_id' => SORT_DESC])
            ->all();
    }
}

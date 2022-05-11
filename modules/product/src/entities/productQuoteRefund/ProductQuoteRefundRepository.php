<?php

namespace modules\product\src\entities\productQuoteRefund;

use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuoteFlight;
use src\repositories\NotFoundException;

class ProductQuoteRefundRepository
{
    public function find(int $id): ProductQuoteRefund
    {
        $refund = ProductQuoteRefund::find()->andWhere(['pqr_id' => $id])->one();
        if ($refund) {
            return $refund;
        }
        throw new NotFoundException('Product Quote Refund not found. ID: ' . $id);
    }

    public function save(ProductQuoteRefund $refund): void
    {
        if (!$refund->save()) {
            throw new \RuntimeException('Product Quote Refund save failed: ' . $refund->getErrorSummary(true)[0]);
        }
    }

    /**
     * @param string $bookingId
     * @param array $statuses
     * @return ProductQuoteRefund[]
     */
    public function findAllByBookingId(string $bookingId, array $statuses): array
    {
        return ProductQuoteRefund::find()
            ->innerJoin(FlightQuote::tableName(), 'fq_product_quote_id = pqr_product_quote_id')
            ->innerJoin(FlightQuoteFlight::tableName(), 'fqf_fq_id = fq_id')
            ->where(['fqf_booking_id' => $bookingId])
            ->byStatuses($statuses)
            ->orderBy(['pqr_id' => SORT_DESC])
            ->all();
    }
}

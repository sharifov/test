<?php

namespace modules\product\src\entities\productQuote;

use modules\product\src\entities\product\Product;
use modules\product\src\entities\productType\ProductType;

/**
 * @see ProductQuote
 */
class Scopes extends \yii\db\ActiveQuery
{
    public function byOrderId(int $orderId): self
    {
        return $this->andWhere(['pq_order_id' => $orderId]);
    }

    public function applied(): self
    {
        return $this->andWhere(['pq_status_id' => ProductQuoteStatus::APPLIED]);
    }

    public function inProgress(): self
    {
        return $this->andWhere(['pq_status_id' => ProductQuoteStatus::IN_PROGRESS]);
    }

    public function booked(): self
    {
        return $this->andWhere(['pq_status_id' => ProductQuoteStatus::BOOKED]);
    }

    public function flightQuotes(): self
    {
        return $this->innerJoin(Product::tableName(), 'pq_product_id = pr_id and pr_type_id = :productTypeId', ['productTypeId' => ProductType::PRODUCT_FLIGHT]);
    }

    public function exceptFlightQuotes(): self
    {
        return $this->innerJoin(Product::tableName(), 'pq_product_id = pr_id and pr_type_id <> :productTypeId', ['productTypeId' => ProductType::PRODUCT_FLIGHT]);
    }

    /**
     * @param null $db
     * @return array|ProductQuote|null
     */
    public function one($db = null)
    {
        return parent::one($db); // TODO: Change the autogenerated stub
    }

    /**
     * @param null $db
     * @return array|ProductQuote[]
     */
    public function all($db = null)
    {
        return parent::all($db); // TODO: Change the autogenerated stub
    }
}

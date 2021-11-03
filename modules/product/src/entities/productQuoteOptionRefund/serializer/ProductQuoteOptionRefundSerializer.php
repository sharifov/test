<?php

namespace modules\product\src\entities\productQuoteOptionRefund\serializer;

use modules\product\src\entities\productQuoteOptionRefund\ProductQuoteOptionRefund;
use modules\product\src\entities\productQuoteOptionRefund\ProductQuoteOptionRefundStatus;

/**
 * Class ProductQuoteOptionRefundSreializer
 * @package modules\product\src\entities\productQuoteOptionRefund\serializer
 *
 * @property ProductQuoteOptionRefund $model
 */
class ProductQuoteOptionRefundSerializer extends \sales\entities\serializer\Serializer
{
    public function __construct(ProductQuoteOptionRefund $model)
    {
        parent::__construct($model);
    }

    /**
     * @inheritDoc
     */
    public static function fields(): array
    {
        return [
            'pqor_selling_price',
            'pqor_penalty_amount',
            'pqor_processing_fee_amount',
            'pqor_refund_amount',
            'pqor_client_currency',
            'pqor_client_selling_price',
            'pqor_client_refund_amount',
            'pqor_refund_allow',
        ];
    }

    /**
     * @inheritDoc
     */
    public function getData(): array
    {
        $data = $this->toArray();

        $data['pqor_status_name'] = ProductQuoteOptionRefundStatus::getName($this->model->pqor_status_id);

        return $data;
    }
}

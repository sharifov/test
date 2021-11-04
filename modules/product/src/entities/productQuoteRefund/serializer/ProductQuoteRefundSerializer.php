<?php

namespace modules\product\src\entities\productQuoteRefund\serializer;

use modules\product\src\entities\productQuoteRefund\ProductQuoteRefund;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundStatus;
use sales\entities\serializer\Serializer;

/**
 * Class ProductQuoteRefundSerializer
 *
 * @property ProductQuoteRefund $model
 */
class ProductQuoteRefundSerializer extends Serializer
{
    /**
     * ProductQuoteRefundSerializer constructor.
     * @param ProductQuoteRefund $model
     */
    public function __construct(ProductQuoteRefund $model)
    {
        parent::__construct($model);
    }

    /**
     * @inheritDoc
     */
    public static function fields(): array
    {
        return [
            'pqr_gid',
            'pqr_selling_price',
            'pqr_penalty_amount',
            'pqr_processing_fee_amount',
            'pqr_refund_amount',
            'pqr_client_currency',
            'pqr_client_currency_rate',
            'pqr_client_selling_price',
            'pqr_client_refund_amount',
            'pqr_cid',
            'pqr_client_penalty_amount',
            'pqr_client_processing_fee_amount'
        ];
    }

    public function getData(): array
    {
        $data = $this->toArray();

        $data['pqr_status_name'] = ProductQuoteRefundStatus::getName($this->model->pqr_status_id);
        if ($objects = $this->model->productQuoteObjectRefunds) {
            foreach ($objects as $object) {
                $data['objects'][] = $object->serialize();
            }
        }
        if ($options = $this->model->productQuoteOptionRefunds) {
            foreach ($options as $option) {
                $data['options'][] = $option->serialize();
            }
        }

        return $data;
    }
}

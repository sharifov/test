<?php

namespace modules\order\src\entities\orderRefund\serializer;

use modules\order\src\entities\orderRefund\OrderRefund;
use modules\order\src\entities\orderRefund\OrderRefundStatus;

/**
 * Class OrderRefundSerializer
 * @package modules\order\src\entities\orderRefund\serializer
 *
 * @property OrderRefund $model
 */
class OrderRefundSerializer extends \sales\entities\serializer\Serializer
{
    public function __construct(OrderRefund $model)
    {
        parent::__construct($model);
    }

    /**
     * @inheritDoc
     */
    public static function fields(): array
    {
        return [
            'orr_uid',
            'orr_selling_price',
            'orr_penalty_amount',
            'orr_processing_fee_amount',
            'orr_charge_amount',
            'orr_refund_amount',
            'orr_client_currency',
            'orr_client_selling_price',
            'orr_client_charge_amount',
            'orr_client_refund_amount',
            'orr_description',
            'orr_expiration_dt'
        ];
    }

    /**
     * @inheritDoc
     */
    public function getData(): array
    {
        $data = $this->toArray();

        $data['orr_status_name'] = OrderRefundStatus::getName($this->model->orr_status_id);

        return $data;
    }
}

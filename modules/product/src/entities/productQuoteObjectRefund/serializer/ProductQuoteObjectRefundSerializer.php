<?php

namespace modules\product\src\entities\productQuoteObjectRefund\serializer;

use modules\flight\src\entities\flightQuoteTicketRefund\FlightQuoteTicketRefund;
use modules\product\src\entities\productQuoteObjectRefund\ProductQuoteObjectRefund;
use modules\product\src\entities\productQuoteObjectRefund\ProductQuoteObjectRefundStatus;

/**
 * Class ProductQuoteObjectRefundSerializer
 * @package modules\product\src\entities\productQuoteObjectRefund\serializer
 *
 * @property ProductQuoteObjectRefund $model
 */
class ProductQuoteObjectRefundSerializer extends \src\entities\serializer\Serializer
{
    public function __construct(ProductQuoteObjectRefund $model)
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
            'pqor_refund_amount',
            'pqor_title',
        ];
    }

    /**
     * @inheritDoc
     */
    public function getData(): array
    {
        $data = $this->toArray();

        $data['pqor_status_name'] = ProductQuoteObjectRefundStatus::getName($this->model->pqor_status_id);
        if ($this->model->productQuoteRefund->productQuote->isFlight()) {
            $data['ticket_refund_number'] = (FlightQuoteTicketRefund::findOne(['fqtr_id' => $this->model->pqor_quote_object_id]))->fqtr_ticket_number ?? '';
        }

        return $data;
    }
}

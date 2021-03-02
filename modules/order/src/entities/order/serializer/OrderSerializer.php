<?php

namespace modules\order\src\entities\order\serializer;

use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderPayStatus;
use modules\order\src\entities\order\OrderStatus;
use modules\product\src\entities\productQuote\ProductQuote;
use sales\entities\serializer\Serializer;

/**
 * Class OrderSerializer
 * @package modules\order\src\entities\order\serializer
 *
 * @property Order $model
 */
class OrderSerializer extends Serializer
{
    public function __construct(Order $model)
    {
        parent::__construct($model);
    }

    public static function fields(): array
    {
        return [
            'or_id',
            'or_gid',
            'or_uid',
            'or_name',
            'or_description',
            'or_status_id',
            'or_status_name',
            'or_pay_status_id',
            'or_pay_status_name',
            'or_app_total',
            'or_app_markup',
            'or_agent_markup',
            'or_client_total',
            'or_client_currency',
            'or_client_currency_rate',
        ];
    }

    public function getData(): array
    {
        $data = $this->toArray();

        $data['or_status_name'] = OrderStatus::getName($this->model->or_status_id);
        $data['or_pay_status_name'] = OrderPayStatus::getName($this->model->or_pay_status_id);

        if ($this->model->or_request_data) {
            $uid = $this->model->or_request_data['uid'] ?? null;
            $data['or_request_uid'] = $uid;
        }

        $data['billing_info'] = [];

        foreach ($this->model->billingInfo as $billingInfo) {
            $data['billing_info'][] = $billingInfo->serialize();
        }

        $data['quotes'] = [];
        if ($quotes = $this->model->productQuotes) {
            /** @var ProductQuote[] $quotes */
            foreach ($quotes as $quote) {
                $quoteData['product'] = $quote->pqProduct->serialize();
                $productQuoteOptionsData = [];
                if ($productQuoteOptions = $quote->productQuoteOptions) {
                    foreach ($productQuoteOptions as $productQuoteOption) {
                        $productQuoteOptionsData[] = $productQuoteOption->serialize();
                    }
                }
                $data['quotes'][] = array_merge(
                    $quote->serialize(),
                    ['product' => $quote->pqProduct->serialize()],
                    ['productQuoteOptions' => $productQuoteOptionsData]
                );
            }
        }

        return $data;
    }
}

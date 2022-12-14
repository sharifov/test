<?php

namespace modules\order\src\entities\order\serializer;

use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderFiles;
use modules\order\src\entities\order\OrderPayStatus;
use modules\order\src\entities\order\OrderStatus;
use modules\product\src\entities\productQuote\ProductQuote;
use src\entities\serializer\Serializer;
use yii\helpers\ArrayHelper;

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
            'or_pay_status_id',
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
        $data['or_client_currency_symbol'] = $this->model->orClientCurrency->cur_symbol ?? null;
        $data['or_files'] = (new OrderFiles())->getList($this->model);

        if ($this->model->or_request_data) {
            $data['or_request_uid'] = ArrayHelper::getValue($this->model->or_request_data, 'Request.request_uid') ??
                ArrayHelper::getValue($this->model->or_request_data, 'Request.FlightRequest.uid');
        } else {
            $data['or_request_uid'] = null;
        }

        $data['billing_info'] = [];

        foreach ($this->model->billingInfo as $billingInfo) {
            $data['billing_info'][] = $billingInfo->serialize();
        }

        $data['quotes'] = [];
        if ($quotes = $this->model->productQuotes) {
            /** @var ProductQuote[] $quotes */
            foreach ($quotes as $quote) {
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

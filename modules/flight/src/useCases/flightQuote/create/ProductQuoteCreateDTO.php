<?php

namespace modules\flight\src\useCases\flightQuote\create;

use modules\flight\models\Flight;
use src\dto\product\ProductQuoteDTO;
use src\helpers\product\ProductQuoteHelper;

/**
 * Class ProductQuoteCreateDTO
 * @package modules\flight\src\useCases\flightQuote\create
 *
 * @property Flight $flight
 * @property array $quote
 */
class ProductQuoteCreateDTO extends ProductQuoteDTO
{
    /**
     * ProductQuoteCreateDTO constructor.
     * @param Flight $flight
     * @param array $quote
     * @param int $userId
     */
    public function __construct(Flight $flight, array $quote, ?int $userId)
    {
        $this->name = $flight->flProduct->pr_name;
        $this->productId = $flight->fl_product_id;
        $this->orderId = null;
        $this->description = null;
        $this->price = null;
        $this->originPrice = null;
        $this->clientPrice = null;
        $this->serviceFeeSum = null;
        $this->originCurrency = $quote['currency'] ?? null;
        $this->clientCurrency = ProductQuoteHelper::getClientCurrencyCode($flight->flProduct);
        $this->originCurrencyRate = $quote['originRate'] ?? 1;
        $this->clientCurrencyRate = ProductQuoteHelper::getClientCurrencyRate($flight->flProduct);
        $this->ownerUserId = $userId;
        $this->createdUserId = $userId;
        $this->updatedUserId = $userId;
    }
}

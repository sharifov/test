<?php

namespace modules\flight\src\useCases\sale\dto;

use common\models\Currency;
use modules\flight\models\Flight;
use sales\dto\product\ProductQuoteDTO;
use sales\helpers\product\ProductQuoteHelper;

/**
 * Class ProductQuoteCreateDTO
 * @package modules\flight\src\useCases\flightQuote\create
 *
 * @property Flight $flight
 * @property array $quote
 */
class ProductQuoteCreateFromSaleDto extends ProductQuoteDTO
{
    /**
     * @param Flight $flight
     * @param int|null $orderId
     * @param float|null $originPrice
     * @param float|null $clientPrice
     * @param string|null $currency
     * @param int|null $userId
     */
    public function __construct(
        Flight $flight,
        ?int $orderId,
        ?float $originPrice,
        ?float $clientPrice,
        ?string $currency,
        ?int $userId = null
    ) {
        $this->name = $flight->flProduct->pr_name;
        $this->productId = $flight->fl_product_id;
        $this->orderId = $orderId;
        $this->description = null;
        $this->price = null;
        $this->originPrice = $originPrice;
        $this->clientPrice = $clientPrice;
        $this->serviceFeeSum = null;
        $this->originCurrency = $currency ?? ProductQuoteHelper::getClientCurrencyCode($flight->flProduct);
        $this->clientCurrency = $currency ?? ProductQuoteHelper::getClientCurrencyCode($flight->flProduct);
        $this->originCurrencyRate = Currency::getDefaultClientCurrencyRate();
        $this->clientCurrencyRate = ProductQuoteHelper::getClientCurrencyRate($flight->flProduct);
        $this->ownerUserId = $userId;
        $this->createdUserId = $userId;
        $this->updatedUserId = $userId;
    }
}

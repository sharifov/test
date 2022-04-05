<?php

namespace modules\flight\src\useCases\sale\dto;

use common\models\Currency;
use modules\flight\models\Flight;
use src\dto\product\ProductQuoteDTO;
use src\helpers\product\ProductQuoteHelper;
use src\services\CurrencyHelper;

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
        $currencyRate = $currency ? CurrencyHelper::getAppRateByCode($currency) : ProductQuoteHelper::getClientCurrencyRate($flight->flProduct);
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
        $this->originCurrencyRate = 1 / $currencyRate;
        $this->clientCurrencyRate = $currencyRate;
        $this->ownerUserId = $userId;
        $this->createdUserId = $userId;
        $this->updatedUserId = $userId;
    }
}

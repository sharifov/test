<?php

namespace sales\dto\product;

use modules\flight\models\Flight;

/**
 * Class ProductQuoteDTO
 * @package sales\dto\productQuote
 *
 * @property $gid
 * @property $name
 * @property $productId
 * @property $orderId
 * @property $description
 * @property $statusId
 * @property $price
 * @property $originPrice
 * @property $clientPrice
 * @property $serviceFeeSum
 * @property $originCurrency
 * @property $clientCurrency
 * @property $originCurrencyRate
 * @property $clientCurrencyRate
 * @property $ownerUserId
 * @property $createdUserId
 * @property $updatedUserId
 * @property $createdDt
 * @property $updatedDt
 */
abstract class ProductQuoteDTO
{
	public $gid;
	public $name;
	public $productId;
	public $orderId;
	public $description;
	public $statusId;
	public $price;
	public $originPrice;
	public $clientPrice;
	public $serviceFeeSum;
	public $originCurrency;
	public $clientCurrency;
	public $originCurrencyRate;
	public $clientCurrencyRate;
	public $ownerUserId;
	public $createdUserId;
	public $updatedUserId;
	public $createdDt;
	public $updatedDt;
}
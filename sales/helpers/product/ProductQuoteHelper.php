<?php

namespace sales\helpers\product;

use common\models\Currency;
use modules\product\src\entities\product\Product;

class ProductQuoteHelper
{
	/**
	 * @param float $price
	 * @param Product $product
	 * @return false|float
	 */
	public static function calcSystemPrice(float $price, Product $product)
	{
		return self::roundPrice($price * self::getBaseCurrencyRate($product));
	}

	/**
	 * @param float $price
	 * @param Product $product
	 * @return false|float
	 */
	public static function calcClientPrice(float $price, Product $product)
	{
		return self::roundPrice($price * self::getClientCurrencyRate($product));
	}

	/**
	 * @param float $price
	 * @param int $precision
	 * @return false|float
	 */
	public static function roundPrice(float $price, int $precision = 2)
	{
		return round($price, $precision);
	}

	/**
	 * @param Product $product
	 * @return string
	 */
	public static function getClientCurrencyCode(Product $product): string
	{
		$leadPreferences = $product->prLead->leadPreferences;
		if ($leadPreferences && $currency = $leadPreferences->prefCurrency) {
				return $currency->cur_code ?? Currency::getDefaultCurrencyCode();
		}
		return Currency::getDefaultCurrencyCode();
	}

	/**
	 * @param Product $product
	 * @return float
	 */
	public static function getBaseCurrencyRate(Product $product): float
	{
		$leadPreferences = $product->prLead->leadPreferences;
		if ($leadPreferences && $currency = $leadPreferences->prefCurrency) {
			return $currency->cur_base_rate ?? Currency::getDefaultBaseCurrencyRate();
		}
		return Currency::getDefaultBaseCurrencyRate();
	}

	/**
	 * @param Product $product
	 * @return float
	 */
	public static function getClientCurrencyRate(Product $product): float
	{
		$leadPreferences = $product->prLead->leadPreferences;
		if ($leadPreferences && $currency = $leadPreferences->prefCurrency) {
				return $currency->cur_app_rate ?? Currency::getDefaultClientCurrencyRate();
		}
		return Currency::getDefaultClientCurrencyRate();
	}

}
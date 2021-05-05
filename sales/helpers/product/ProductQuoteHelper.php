<?php

namespace sales\helpers\product;

use common\models\Currency;
use common\models\CurrencyHistory;
use modules\product\src\entities\product\Product;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelationQuery;
use modules\product\src\entities\productTypePaymentMethod\ProductTypePaymentMethodQuery;
use sales\services\CurrencyHelper;
use yii\helpers\Html;

class ProductQuoteHelper
{
    /**
     * @param float $price
     * @param string $currencyCode
     * @return false|float
     */
    public static function calcSystemPrice(float $price, string $currencyCode)
    {
        $rate = Currency::getBaseRateByCurrencyCode($currencyCode) ?? CurrencyHistory::getBaseRateByCurrencyCode($currencyCode);

        if ($rate === null) {
            throw new \DomainException('Cant find rate for the currency: ' . $currencyCode);
        }

        return self::roundPrice($price * $rate);
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
        return CurrencyHelper::roundUp($price, $precision);
    }

    /**
     * @param Product $product
     * @return string
     */
    public static function getClientCurrencyCode(Product $product): string
    {
        $leadPreferences = $product->prLead->leadPreferences ?? null;
        if ($leadPreferences && $currency = $leadPreferences->prefCurrency) {
            return $currency->cur_code ?? Currency::getDefaultCurrencyCode();
        }
        return Currency::getDefaultCurrencyCode();
    }

    /**
     * @param Product $product
     * @return float
     */
    public static function getClientCurrencyRate(Product $product): float
    {
        $leadPreferences = $product->prLead->leadPreferences ?? null;
        if ($leadPreferences && $currency = $leadPreferences->prefCurrency) {
            return $currency->cur_app_rate ?? Currency::getDefaultClientCurrencyRate();
        }
        return Currency::getDefaultClientCurrencyRate();
    }

    public static function displayAlternativeQuoteIcon(): string
    {
        return Html::tag('i', '', [
            'class' => 'fab fa-autoprefixer',
            'title' => 'Alternative Quote',
            'data-toggle' => 'tooltip'
        ]);
    }

    public static function displayOriginQuoteIcon(int $productQuoteId): string
    {
        $alternativeQuotes = ProductQuoteRelationQuery::getAlternativeQuoteIdsByOrigin($productQuoteId);

        $title = 'Origin Quote';
        if ($alternativeQuotes) {
            $title .= '; Related Alternative Quotes: ' . implode(', ', $alternativeQuotes);
        }
        return Html::tag('i', '', [
            'class' => 'fas fa-object-ungroup',
            'title' => $title,
            'data-toggle' => 'tooltip'
        ]);
    }

    public static function displayOriginOrAlternativeIcon(ProductQuote $productQuote): string
    {
        if ($productQuote->isAlternative()) {
            return self::displayAlternativeQuoteIcon();
        }

        if ($productQuote->isOrigin()) {
            return self::displayOriginQuoteIcon($productQuote->pq_id);
        }

        return '';
    }
}

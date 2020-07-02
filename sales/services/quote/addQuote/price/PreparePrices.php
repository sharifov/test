<?php


namespace sales\services\quote\addQuote\price;


use common\models\Lead;
use common\models\Quote;
use common\models\QuotePrice;

/**
 * Class PreparePrices
 */
class PreparePrices
{
    /**
     * @param Lead $lead
     * @param array $pricesFromDump
     * @return array
     */
    public static function prepareByLeadPax(Lead $lead, array $pricesFromDump): array
    {
        $prices = [];
        foreach ($lead->getPaxTypes() as $type) {
            $price = null;

            foreach ($pricesFromDump as $key => $value) {
                if ($type === $value['type']) {
                    $price = new QuotePrice();
                    $price->passenger_type = $type;
                    $price->fare = $value['fare'];
                    $price->taxes = $value['taxes'];
                    $price->net = $price->fare + $price->taxes;
                    $price->selling = ($price->net + $price->mark_up)  * (1 + (new Quote())->serviceFee);
                    $price->toFloat();
                    $price->roundAttributesValue();
                    $price->oldParams = serialize($price->attributes);

                    $prices[] = $price;
                    unset($pricesFromDump[$key]);
                    break;
                }
            }
            if ($price === null) {
                $price = new QuotePrice();
                $price->createQPrice($type);
                $prices[] = $price;
            }
        }
        return $prices;
    }
}
<?php

namespace modules\flight\src\useCases\flightQuote\createManually\helpers;

use modules\flight\models\Flight;
use modules\flight\models\FlightPax;
use modules\flight\src\useCases\flightQuote\createManually\FlightQuotePaxPriceForm;
use modules\flight\src\useCases\flightQuote\createManually\VoluntaryQuotePaxPriceForm;
use modules\flight\src\useCases\voluntaryExchangeManualCreate\form\VoluntaryQuoteCreateForm;
use modules\product\src\entities\productQuote\ProductQuote;
use src\helpers\product\ProductQuoteHelper;
use yii\helpers\ArrayHelper;

/**
 * Class FlightQuotePaxPriceHelper
 */
class FlightQuotePaxPriceHelper
{
    public static function getQuotePaxPriceFormCollection(Flight $flight): array
    {
        $prices = [];
        if ($flight->fl_adults) {
            $prices[] = new FlightQuotePaxPriceForm(FlightPax::PAX_ADULT, FlightPax::getPaxId(FlightPax::PAX_ADULT), $flight->fl_adults);
        }
        if ($flight->fl_children) {
            $prices[] = new FlightQuotePaxPriceForm(FlightPax::PAX_CHILD, FlightPax::getPaxId(FlightPax::PAX_CHILD), $flight->fl_children);
        }
        if ($flight->fl_infants) {
            $prices[] = new FlightQuotePaxPriceForm(FlightPax::PAX_INFANT, FlightPax::getPaxId(FlightPax::PAX_INFANT), $flight->fl_infants);
        }
        return $prices;
    }

    public static function getVoluntaryQuotePaxPriceFormCollection(Flight $flight, ?float $systemMarkUp = null): array
    {
        $prices = [];
        if ($flight->fl_adults) {
            $prices[] = new VoluntaryQuotePaxPriceForm(
                FlightPax::PAX_ADULT,
                FlightPax::getPaxId(FlightPax::PAX_ADULT),
                $flight->fl_adults,
                $systemMarkUp
            );
        }
        if ($flight->fl_children) {
            $prices[] = new VoluntaryQuotePaxPriceForm(
                FlightPax::PAX_CHILD,
                FlightPax::getPaxId(FlightPax::PAX_CHILD),
                $flight->fl_children,
                $systemMarkUp
            );
        }
        if ($flight->fl_infants) {
            $prices[] = new VoluntaryQuotePaxPriceForm(
                FlightPax::PAX_INFANT,
                FlightPax::getPaxId(FlightPax::PAX_INFANT),
                $flight->fl_infants,
                $systemMarkUp
            );
        }
        return $prices;
    }

    public static function refreshChangeQuotePrice(VoluntaryQuoteCreateForm $createQuoteForm): VoluntaryQuoteCreateForm
    {
        $oldPrices = unserialize($createQuoteForm->oldPrices, ['allowed_classes' => false]);
        $newPrices = $createQuoteForm->prices;

        foreach ($oldPrices as $oldPrice) {
            foreach ($newPrices as $key => $value) {
                if ((int) $oldPrice['paxCodeId'] === (int) $value['paxCodeId']) {
                    if ((float) $oldPrice['fare'] !== (float) $value['fare']) {
                        $selling = (float) $value['fare'] + (float) $value['taxes'] + $value['systemMarkUp'] + (float) $value['markup'];
                        $value['selling'] = ProductQuoteHelper::roundPrice($selling);
                    } elseif ((float) $oldPrice['taxes'] !== (float) $value['taxes']) {
                        $selling = (float) $value['fare'] + (float) $value['taxes'] + (float) $value['systemMarkUp'] + (float) $value['markup'];
                        $value['selling'] = ProductQuoteHelper::roundPrice($selling);
                    } elseif ((float) $oldPrice['markup'] !== (float) $value['markup']) {
                        $selling = (float)$value['fare'] + (float)$value['taxes'] + (float)$value['systemMarkUp'] + (float)$value['markup'];
                        $value['selling'] = ProductQuoteHelper::roundPrice($selling);
                    }
                    $createQuoteForm->prices[$key] = $value;
                }
            }
        }
        $createQuoteForm->oldPrices = serialize(ArrayHelper::toArray($createQuoteForm->prices));
        return $createQuoteForm;
    }

    public static function calculateVoluntaryPricing(ProductQuote $productQuote): array
    {
        $baseFare = 0.00;
        $baseTax = 0.00;
        $markup = 0.00;
        $price = 0.00;

        if ($flightQuote = $productQuote->flightQuote) {
            foreach ($flightQuote->flightQuotePaxPrices as $key => $flightQuotePaxPrice) {
                $baseFare += $flightQuotePaxPrice->qpp_fare * $flightQuotePaxPrice->qpp_cnt;
                $baseTax += $flightQuotePaxPrice->qpp_tax * $flightQuotePaxPrice->qpp_cnt;
                $markup += ($flightQuotePaxPrice->qpp_system_mark_up + $flightQuotePaxPrice->qpp_agent_mark_up) * $flightQuotePaxPrice->qpp_cnt;
                $price += ($flightQuotePaxPrice->qpp_fare + $flightQuotePaxPrice->qpp_tax +
                    ($flightQuotePaxPrice->qpp_system_mark_up + $flightQuotePaxPrice->qpp_agent_mark_up)) * $flightQuotePaxPrice->qpp_cnt;
            }
        }

        return [
            'baseFare' => $baseFare,
            'baseTax' => $baseTax,
            'markup' => $markup,
            'price' => $price,
        ];
    }

    public static function priceFormat(float $price, int $precision = 2): string
    {
        return number_format(ProductQuoteHelper::roundPrice($price, $precision), $precision);
    }
}

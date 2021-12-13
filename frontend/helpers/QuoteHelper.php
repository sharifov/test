<?php

namespace frontend\helpers;

use common\models\Quote;
use common\models\Airports;
use common\models\Lead;
use yii\helpers\VarDumper;

/**
 * Class QuoteHelper
 */
class QuoteHelper
{
    public const PENALTY_TYPE_LIST = [
        'ex' => 'Exchange',
        're' => 'Refund',
    ];

    public const TOP_META_LIST = [
        self::TOP_META_CHEAPEST => 'Cheapest',
        self::TOP_META_FASTEST => 'Fastest',
        self::TOP_META_BEST => 'Best',
    ];

    public const TOP_META_CHEAPEST = 'cheapest';
    public const TOP_META_BEST = 'best';
    public const TOP_META_FASTEST = 'fastest';

    public static function innerPenalties(array $penalties): string
    {
        $out = '';
        if ($penalties && self::checkPenaltiesInfo($penalties)) {
            $out .= "<div class='tooltip_quote_info_box'>";
            $out .= '<p>Penalties: </p>';

            foreach ($penalties['list'] as $item) {
                $out .= '<ul>';
                if (isset($item['permitted']) && $item['permitted']) {
                    if (!empty($item['type'])) {
                        $out .= '<li>Type : <strong>' . self::getPenaltyTypeName($item['type']) . '</strong></li>';
                    }
                    if (!empty($item['applicability'])) {
                        $out .= '<li>Applicability : <strong>' . $item['applicability'] . '</strong></li>';
                    }
                    if (isset($item['oAmount']['amount'], $item['oAmount']['currency'])) {
                        $out .= '<li>Amount : <strong>' . $item['oAmount']['amount'] . ' ' . $item['oAmount']['currency'] . '</strong></li>';
                    }
                }
                $out .= '</ul>';
            }
            $out .= '</div>';
        }
        return $out;
    }

    public static function formattedPenalties(?array $penalties, string $class = 'quote__badge quote__badge--warning'): string
    {
        if ($penalties && self::checkPenaltiesInfo($penalties)) {
            return '<span class="' . $class . '"
                data-toggle="tooltip"
                data-html="true"
                title="' . self::innerPenalties($penalties) . '">
                    <i class="fa fa-expand"></i>
            </span>';
        }
        return '';
    }

    public static function formattedRanking(?array $meta, string $class = 'quote__badge bg-info'): string
    {
        if (!empty($meta['rank'])) {
            $rank = number_format($meta['rank'], 1, '.', '');
            $rank = ($rank === '10.0') ? 10 : $rank;

            return '<span class="' . $class . '"
                data-toggle="tooltip"
                data-html="true"
                title="Rank: ' . $meta['rank'] . '">
                    ' . $rank . '
            </span>';
        }
        return '';
    }

    public static function formattedCheapest(?array $meta, string $class = 'quote__badge bg-green'): string
    {
        if (!empty($meta['cheapest'])) {
            return '<span class="' . $class . '"
                data-toggle="tooltip"
                data-html="true"
                title="' . self::TOP_META_LIST['cheapest'] . '">
                    <i class="fa fa-money"></i>
            </span>';
        }
        return '';
    }

    public static function formattedFastest(?array $meta, string $class = 'quote__badge bg-orange'): string
    {
        if (!empty($meta['fastest'])) {
            return '<span class="' . $class . '"
                data-toggle="tooltip"
                data-html="true"
                title="' . self::TOP_META_LIST['fastest'] . '">
                    <i class="fa fa-rocket"></i>
            </span>';
        }
        return '';
    }

    public static function formattedBest(?array $meta, string $class = 'quote__badge bg-primary'): string
    {
        if (!empty($meta['best'])) {
            return '<span class="' . $class . '"
                data-toggle="tooltip"
                data-html="true"
                title="' . self::TOP_META_LIST['best'] . '">
                    <i class="fa fa-thumbs-o-up"></i>
            </span>';
        }
        return '';
    }

    public static function formattedFreeBaggage(?array $meta, string $class = 'quote__badge quote__badge--amenities'): string
    {
        if (!empty($meta['bags'])) {
            return '<span class="' . $class . '"
                data-toggle="tooltip"
                title="Free baggage - ' . (int) $meta['bags'] .  ' pcs">
                <i class="fa fa-suitcase"></i>
                <span class="inside_icon">' . (int) $meta['bags'] . '</span>
            </span>';
        }
        return '<span class="' . $class . ' quote__badge--disabled" data-toggle="tooltip" title="" data-original-title="No free baggage">
                <i class="fa fa-suitcase"></i>
            </span>';
    }

    public static function formattedMetaRank(?array $meta): string
    {
        $out = '';
        $out .= self::formattedRanking($meta);
        $out .= self::formattedCheapest($meta);
        $out .= self::formattedFastest($meta);
        $out .= self::formattedBest($meta);
        return $out;
    }

    public static function formattedProviderProject(Quote $quote, string $class = 'quote__badge bg-secondary'): string
    {
        if ($quote->providerProject) {
            return '<span class="' . $class . '"
                data-toggle="tooltip"
                data-html="true"
                title="' . $quote->providerProject->name . '">
                    P
            </span>';
        }
        return '';
    }

    public static function checkPenaltiesInfo(array $penalties): bool
    {
        return (!empty($penalties['list']));
    }

    public static function getPenaltyTypeName(string $keyType): string
    {
        if (array_key_exists($keyType, self::PENALTY_TYPE_LIST)) {
            return self::PENALTY_TYPE_LIST[$keyType];
        }
        return 'unknown type';
    }

    /**
     * @param array $quotes
     * @return array
     */
    public static function formatQuoteData(array $quotes): array
    {
        self::getQuotePriceRange($quotes);

        $connectionAirports = [];
        foreach ($quotes['results'] as $key => $quote) {
            $quotes['results'][$key]['price'] = self::getQuotePrice($quote);
            $quotes['results'][$key]['originRate'] = self::getOriginRate($quote);
            $preSegment = null;
            $baggagePerSegment = [];
            $freeBaggage = false;
            $airportChange = false;
            $technicalStopCnt = 0;
            $time = [];
            $stops = [];
            $totalDuration = [];
            $bagFilter = '';
            $cnt = 0;
            foreach ($quote['trips'] as $trip) {
                if (isset($trip['duration'])) {
                    $totalDuration[] = $trip['duration'];
                    $quotes['totalDuration'][] = $trip['duration'];
                    $quotes['tripsDurations'][$cnt][] = $trip['duration'];
                }
                $cnt++;
                $stopCnt = count($trip['segments']) - 1;

                foreach ($trip['segments'] as $segment) {
                    if (isset($segment['stop']) && $segment['stop'] > 0) {
                        $stopCnt += $segment['stop'];
                        $technicalStopCnt += $segment['stop'];
                    }

                    if ($preSegment !== null && $segment['departureAirportCode'] != $preSegment['arrivalAirportCode']) {
                        $airportChange = true;
                    }
                    if ($segment['departureAirportCode'] || $segment['arrivalAirportCode']) {
                        if (!in_array($segment['departureAirportCode'], $connectionAirports) && $airportObj = Airports::findByIata($segment['departureAirportCode'])) {
                            $connectionAirports[$segment['departureAirportCode']] = $airportObj->cityName . ' ' . $segment['departureAirportCode'];
                        }
                        if (!in_array($segment['arrivalAirportCode'], $connectionAirports) && $airportObj = Airports::findByIata($segment['arrivalAirportCode'])) {
                            $connectionAirports[$segment['arrivalAirportCode']] = $airportObj->cityName . ' ' . $segment['arrivalAirportCode'];
                        }
                    }

                    if (isset($segment['baggage']) && $freeBaggage === false) {
                        foreach ($segment['baggage'] as $baggage) {
                            if (isset($baggage['allowPieces'])) {
                                $baggagePerSegment[] = $baggage['allowPieces'];
                            }
                        }
                    }
                    $preSegment = $segment;
                }

                $firstSegment = $trip['segments'][0];
                $lastSegment = end($trip['segments']);
                $time[] = ['departure' => $firstSegment['departureTime'],'arrival' => $lastSegment['arrivalTime']];
                $stops[] = $stopCnt;
            }

            if (!empty($baggagePerSegment)) {
                if (min($baggagePerSegment) == 1) {
                    $bagFilter = 1;
                } elseif ((min($baggagePerSegment) == 2)) {
                    $bagFilter = 2;
                }
            }

            $quotes['results'][$key]['stops'] = $stops;
            $quotes['results'][$key]['time'] = $time;
            $quotes['results'][$key]['bagFilter'] = $bagFilter ?? '';
            $quotes['results'][$key]['airportChange'] = $airportChange;
            $quotes['results'][$key]['technicalStopCnt'] = $technicalStopCnt;
            $quotes['results'][$key]['duration'] = $totalDuration;
            $quotes['results'][$key]['totalDuration'] = array_sum($totalDuration);
            $quotes['results'][$key]['topCriteria'] = self::getQuoteTopCriteria($quote);
            $quotes['results'][$key]['rank'] = self::getQuoteRank($quote);
        }

        asort($connectionAirports);
        $quotes['connectionAirports'] = $connectionAirports;
        $quotes['tripsMinDurationsInMinutes'] = $quotes['tripsMaxDurationsInMinutes'] = $quotes['tripMaxDurationRoundHours'] = $quotes['tripMaxDurationRoundMinutes'] = [];
        if (!empty($quotes['tripsDurations'])) {
            foreach ($quotes['tripsDurations'] as $key => $tripDurations) {
                $quotes['tripsMinDurationsInMinutes'][$key] = min($tripDurations) > 0 ? min($tripDurations) : 0;
                $quotes['tripsMaxDurationsInMinutes'][$key] = max($tripDurations) > 0 ? max($tripDurations) : 0;
                $quotes['tripMaxDurationRoundHours'][$key] = floor($quotes['tripsMaxDurationsInMinutes'][$key] / 60);
                if ($quotes['tripsMaxDurationsInMinutes'][$key] % 60 > 50) {
                    $quotes['tripMaxDurationRoundHours'][$key]++;
                }
                $quotes['tripMaxDurationRoundMinutes'][$key] = ceil($quotes['tripsMaxDurationsInMinutes'][$key] / 10) * 10 % 60;
            }
        }

        return self::sortByTopCriteria($quotes);
    }

    private static function sortByTopCriteria($quotes)
    {
        $combinationOne = [];
        $combinationTwo = [];
        $combinationThree = [];
        $combinationFour = [];
        $combinationFive = [];
        $combinationSix = [];
        $combinationSeven = [];
        $combinationEight = [];

        foreach ($quotes['results'] as $quote) {
            $meta = $quote['meta'];
            if ($meta['cheapest'] && $meta['fastest'] && $meta['best']) {
                $combinationOne[] = $quote;
                continue;
            }
            if ($meta['cheapest'] && $meta['fastest'] && !$meta['best']) {
                $combinationTwo[] = $quote;
                continue;
            }
            if ($meta['cheapest'] && !$meta['fastest'] && $meta['best']) {
                $combinationThree[] = $quote;
                continue;
            }
            if (!$meta['cheapest'] && $meta['fastest'] && $meta['best']) {
                $combinationFour[] = $quote;
                continue;
            }
            if ($meta['cheapest'] && !$meta['fastest'] && !$meta['best']) {
                $combinationFive[] = $quote;
                continue;
            }
            if (!$meta['cheapest'] && $meta['fastest'] && !$meta['best']) {
                $combinationSix[] = $quote;
                continue;
            }
            if (!$meta['cheapest'] && !$meta['fastest'] && $meta['best']) {
                $combinationSeven[] = $quote;
                continue;
            }
            if (!$meta['cheapest'] && !$meta['fastest'] && !$meta['best']) {
                $combinationEight[] = $quote;
            }
        }

        $quotes['results'] = array_merge(
            self::sortAscByPrice($combinationOne),
            self::sortAscByPrice($combinationTwo),
            self::sortAscByPrice($combinationThree),
            self::sortAscByPrice($combinationFour),
            self::sortAscByPrice($combinationFive),
            self::sortAscByPrice($combinationSix),
            self::sortAscByPrice($combinationSeven),
            self::sortAscByPrice($combinationEight)
        );

        return $quotes;
    }

    private static function sortAscByPrice(array $data)
    {
        usort($data, function ($item1, $item2) {
            return self::getQuotePrice($item1) <=> self::getQuotePrice($item2);
        });
        return $data;
    }

    /**
     * @param array $quotes
     */
    private static function getQuotePriceRange(array &$quotes): void
    {
        $price = $minPrice = $maxPrice = 0;

        foreach ($quotes['results'] as $key => $quote) {
            if (isset($quote['passengers']['ADT'])) {
                $price = $quote['passengers']['ADT']['price'];
            } elseif (isset($quote['passengers']['CHD'])) {
                $price = $quote['passengers']['CHD']['price'];
            } elseif (isset($quote['passengers']['INF'])) {
                $price = $quote['passengers']['INF']['price'];
            }
            if ($key == 0) {
                $minPrice = $maxPrice = $price;
            }

            if ($price < $minPrice) {
                $minPrice = $price;
            }

            if ($price > $maxPrice) {
                $maxPrice = $price;
            }
        }

        $quotes['minPrice'] = $minPrice;
        $quotes['maxPrice'] = $maxPrice;
    }

    /**
     * @param array $quote
     * @return mixed
     */
    private static function getQuotePrice(array $quote)
    {
        $price = $quote['prices']['totalPrice'];
        if (isset($quote['passengers']['ADT'])) {
            $price = $quote['passengers']['ADT']['price'];
        } elseif (isset($quote['passengers']['CHD'])) {
            $price = $quote['passengers']['CHD']['price'];
        } elseif (isset($quote['passengers']['INF'])) {
            $price = $quote['passengers']['INF']['price'];
        }
        return $price;
    }

    /**
     * @param array $quote
     * @return mixed|null
     */
    public static function getOriginRate(array $quote)
    {
        $rate = null;
        if (!empty($quote['currencies']) && !empty($quote['currencyRates'])) {
            foreach ($quote['currencies'] as $currency) {
                if (!empty($quote['currencyRates'][$currency . $currency])) {
                    $rate = $quote['currencyRates'][$currency . $currency]['rate'] ?? null;
                }
            }
        }
        return $rate;
    }

    private static function getQuoteTopCriteria(array $quote): string
    {
        $topCriteria = '';
        if (!empty($quote['meta']['fastest'])) {
            $topCriteria .= self::TOP_META_FASTEST;
        }
        if (!empty($quote['meta']['best'])) {
            $topCriteria .= self::TOP_META_BEST;
        }
        if (!empty($quote['meta']['cheapest'])) {
            $topCriteria .= self::TOP_META_CHEAPEST;
        }
        return $topCriteria;
    }

    private static function getQuoteRank(array $quote): float
    {
        if (!empty($quote['meta']['rank'])) {
            return $quote['meta']['rank'];
        }
        return 0.0;
    }
}

<?php

namespace frontend\helpers;

use common\models\Quote;

/**
 * Class QuoteHelper
 */
class QuoteHelper
{
    public const PENALTY_TYPE_LIST = [
        'ex' => 'Exchange',
        're' => 'Refund',
    ];

    public const RANK_INFO_LIST = [
        'rank' => 'Rank',
        'cheapest' => 'Cheapest',
        'fastest' => 'Fastest',
        'best' => 'Best itinerary',
    ];

    public static function formattedPenalties(array $penalties): string
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

    public static function formattedRank(array $meta): string
    {
        $out = '';
        if ($meta && self::checkRankInfo($meta)) {
            $out .= "<div class='tooltip_quote_info_box'>";
            $out .= '<p>Ranking: </p>';
            $out .= '<ul>';

            if (!empty($meta['rank'])) {
                $out .= '<li>Rank : <strong>' . number_format($meta['rank'], 2, '.', '') . '</strong></li>';
            }

            $rankParams = 0;
            $rankParamsOut = '';

            if (isset($meta['cheapest'])) {
                $icoClass = $meta['cheapest'] ? 'fa-check' : 'fa-times';
                $rankParams += (int) $meta['cheapest'];
                $rankParamsOut .= '<li>' . self::RANK_INFO_LIST['cheapest'] . " : <i class='fa " . $icoClass . "'></i></li>";
            }
            if (isset($meta['fastest'])) {
                $icoClass = $meta['fastest'] ? 'fa-check' : 'fa-times';
                $rankParams += (int) $meta['fastest'];
                $rankParamsOut .= '<li>' . self::RANK_INFO_LIST['fastest'] . " : <i class='fa " . $icoClass . "'></i></li>";
            }
            if (isset($meta['best'])) {
                $icoClass = $meta['best'] ? 'fa-check' : 'fa-times';
                $rankParams += (int) $meta['best'];
                $rankParamsOut .= '<li>' . self::RANK_INFO_LIST['best'] . " : <i class='fa " . $icoClass . "'></i></li>";
            }

            $out .= $rankParams ? $rankParamsOut : '';

            $out .= '</ul>';
            $out .= '</div>';
        }
        return $out;
    }

    public static function checkPenaltiesInfo(array $penalties): bool
    {
        return (!empty($penalties['exchange']) || !empty($penalties['refund']));
    }

    public static function checkRankInfo(array $meta): bool
    {
        return !empty(array_intersect_key(self::RANK_INFO_LIST, $meta));
    }

    private static function getPenaltyTypeName(string $keyType): string
    {
        if (array_key_exists($keyType, self::PENALTY_TYPE_LIST)) {
            return self::PENALTY_TYPE_LIST[$keyType];
        }
        return 'unknown type';
    }


    /* TODO::

        * каждый ранкинг сделать отдельной иконкой

        "ранк" - цифра
        "cheapest": денга
        "fastest": ракета
        "best": палец вверх

        * фильтр
        - ранг диапазон
        - остальное выпадающий списко только fastest например

        * группировка
        - маркировать или скрывать(аккордеон) - но выводить полные результаты

        * багажи
        - выводить две цифры - старый алгоритм/новый алгоритм
     */
}

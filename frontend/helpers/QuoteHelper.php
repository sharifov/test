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

    public const TOP_META_LIST = [
        self::TOP_META_CHEAPEST => 'Cheapest',
        self::TOP_META_FASTEST => 'Fastest',
        self::TOP_META_BEST => 'Best',
    ];

    public const TOP_META_CHEAPEST = 'cheapest';
    public const TOP_META_BEST = 'best';
    public const TOP_META_FASTEST = 'fastest';

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

    public static function formattedRanking(?array $meta): string
    {
        if (!empty($meta['rank'])) {

            $rank = number_format($meta['rank'], 1, '.', '');
            $rank = ($rank === '10.0') ? 10 : $rank;

            return '<span class="quote__badge bg-info"
                data-toggle="tooltip"
                data-html="true"
                title="Rank: ' . $meta['rank'] . '">
                    ' . $rank . '
            </span>';
        }
        return '';
    }

    public static function formattedCheapest(?array $meta): string
    {
        if (!empty($meta['cheapest'])) {
            return '<span class="quote__badge bg-green"
                data-toggle="tooltip"
                data-html="true"
                title="' . self::TOP_META_LIST['cheapest'] . '">
                    <i class="fa fa-money"></i>
            </span>';
        }
        return '';
    }

    public static function formattedFastest(?array $meta): string
    {
        if (!empty($meta['fastest'])) {
            return '<span class="quote__badge bg-orange"
                data-toggle="tooltip"
                data-html="true"
                title="' . self::TOP_META_LIST['fastest'] . '">
                    <i class="fa fa-rocket"></i>
            </span>';
        }
        return '';
    }

    public static function formattedBest(?array $meta): string
    {
        if (!empty($meta['best'])) {
            return '<span class="quote__badge bg-primary"
                data-toggle="tooltip"
                data-html="true"
                title="' . self::TOP_META_LIST['best'] . '">
                    <i class="fa fa-thumbs-o-up"></i>
            </span>';
        }
        return '';
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

    public static function checkPenaltiesInfo(array $penalties): bool
    {
        return (!empty($penalties['exchange']) || !empty($penalties['refund']));
    }

    private static function getPenaltyTypeName(string $keyType): string
    {
        if (array_key_exists($keyType, self::PENALTY_TYPE_LIST)) {
            return self::PENALTY_TYPE_LIST[$keyType];
        }
        return 'unknown type';
    }


    /* TODO::

        * фильтр
        - ранг диапазон
        - остальное выпадающий списко только fastest например

        * группировка
        - маркировать или скрывать(аккордеон) - но выводить полные результаты

        * багажи
        - выводить две цифры - старый алгоритм/новый алгоритм
     */
}

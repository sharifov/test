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

    public static function formattedPenalties(Quote $model): string
    {
        $out = '';
        if ($penalties = $model->getPenaltiesInfo()) {
            $out .= "<div class='tooltip_quote_info_box'>";
            $out .= '<p>Penalties: </p>';
            if (!empty($penalties['list'])) {
                foreach ($penalties['list'] as $item) {
                    $out .= '<ul>';
                    if (isset($item['permitted']) && $item['permitted']) {
                        $out .= '<li>Type : <strong>' . self::getPenaltyTypeName($item['type']) . '</strong></li>';
                        $out .= '<li>Applicability : <strong>' . $item['applicability'] . '</strong></li>';
                        $out .= '<li>Amount : <strong>' . $item['oAmount']['amount'] . ' ' . $item['oAmount']['currency'] . '</strong></li>';
                    }
                    $out .= '</ul>';
                }
            }
            $out .= '</div>';
        }
        return $out;
    }

    public static function formattedRank(Quote $model): string
    {
        $out = '';
        if (($meta = $model->getMetaInfo()) && self::checkRankInfo($meta)) {
            $out .= "<div class='tooltip_quote_info_box'>";
            $out .= '<p>Ranking: </p>';
            $out .= '<ul>';

            if (!empty($meta['rank'])) {
                $out .= '<li>Rank : <strong>' . number_format($meta['rank'], 2, '.', '') . '</strong></li>';
            }
            if (isset($meta['cheapest'])) {
                $icoClass = $meta['cheapest'] ? 'fa-check' : 'fa-times' ;
                $out .= '<li>' . self::RANK_INFO_LIST['cheapest'] . " : <i class='fa " . $icoClass . "'></i></li>";
            }
            if (isset($meta['fastest'])) {
                $icoClass = $meta['fastest'] ? 'fa-check' : 'fa-times' ;
                $out .= '<li>' . self::RANK_INFO_LIST['fastest'] . " : <i class='fa " . $icoClass . "'></i></li>";
            }
            if (isset($meta['best'])) {
                $icoClass = $meta['best'] ? 'fa-check' : 'fa-times' ;
                $out .= '<li>' . self::RANK_INFO_LIST['best'] . " : <i class='fa " . $icoClass . "'></i></li>";
            }

            $out .= '</ul>';
            $out .= '</div>';
        }
        return $out;
    }

    private static function checkRankInfo(array $meta): bool
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

}

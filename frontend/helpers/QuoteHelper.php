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

    public static function formattedPenalties(Quote $model): string
    {
        $out = '';
        if ($penalties = $model->getPenaltiesInfo()) {
            $out .= "<div class='tooltip_penalties_box'>";
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

    private static function getPenaltyTypeName(string $keyType): string
    {
        if (array_key_exists($keyType, self::PENALTY_TYPE_LIST)) {
            return self::PENALTY_TYPE_LIST[$keyType];
        }
        return 'unknown type';
    }

}

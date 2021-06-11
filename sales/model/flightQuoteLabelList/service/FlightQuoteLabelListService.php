<?php

namespace sales\model\flightQuoteLabelList\service;

use sales\model\flightQuoteLabelList\entity\FlightQuoteLabelList;
use yii\db\Expression;

/**
 * Class FlightQuoteLabelListService
 */
class FlightQuoteLabelListService
{
    public static function getListKeyDescrition(?array $labels = null, int $cacheDuration = 30)
    {
        $query = FlightQuoteLabelList::find()
            ->addSelect([
                'description' => new Expression("
                    CASE 
                        WHEN 
                            fqll_description IS NULL OR fqll_description = ''
                        THEN 
                            CASE 
                                WHEN 
                                    fqll_origin_description IS NULL OR fqll_origin_description = ''
                                THEN 
                                    fqll_label_key
                                ELSE fqll_origin_description 
                            END
                        ELSE fqll_description 
                    END")
            ]);

        if ($labels) {
            $query->where(['IN', 'fqll_label_key', $labels]);
        }

        return $query->indexBy('fqll_label_key')
            ->orderBy(['fqll_description' => SORT_ASC])
            ->asArray()
            ->cache($cacheDuration)
            ->column();
    }
}

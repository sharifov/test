<?php

namespace sales\model\flightQuoteLabelList\service;

use sales\model\flightQuoteLabelList\entity\FlightQuoteLabelList;
use yii\db\Expression;

/**
 * Class FlightQuoteLabelListService
 */
class FlightQuoteLabelListService
{
    public static function getListKeyDescrition(int $cacheDuration = 30)
    {
        return FlightQuoteLabelList::find()
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
            ])
            ->indexBy('fqll_label_key')
            ->asArray()
            ->cache($cacheDuration)
            ->column();
    }
}

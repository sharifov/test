<?php

namespace sales\helpers\lead;

use common\models\LeadFlightSegment;
use yii\helpers\ArrayHelper;

class LeadFlightSegmentHelper
{

    /**
     * @return array
     */
    public static function flexibilityList(): array
    {
        return [
            0 => 0,
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4
        ];
    }

    /**
     * @return array
     */
    public static function flexibilityTypeList(): array
    {
        return [
            LeadFlightSegment::FLEX_TYPE_MINUS => '-',
            LeadFlightSegment::FLEX_TYPE_PLUS => '+',
            LeadFlightSegment::FLEX_TYPE_PLUS_MINUS => '+/-',
        ];
    }

    /**
     * @param string $type
     * @return string
     */
    public static function flexibilityTypeName(string $type): string
    {
        return ArrayHelper::getValue(self::flexibilityTypeList(), $type);
    }

}

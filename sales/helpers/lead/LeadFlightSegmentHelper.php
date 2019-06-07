<?php

namespace sales\helpers\lead;

use common\models\LeadFlightSegment;
use yii\helpers\ArrayHelper;

class LeadFlightSegmentHelper
{

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

    public static function flexibilityTypeList(): array
    {
        return [
            LeadFlightSegment::FLEX_TYPE_MINUS => '-',
            LeadFlightSegment::FLEX_TYPE_PLUS => '+',
            LeadFlightSegment::FLEX_TYPE_PLUS_MINUS => '+/-',
        ];
    }

    public static function flexibilityTypeName($type): string
    {
        return ArrayHelper::getValue(self::flexibilityTypeList(), $type);
    }

}

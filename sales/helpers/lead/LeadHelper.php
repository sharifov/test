<?php

namespace sales\helpers\lead;

use common\models\Lead;
use yii\helpers\ArrayHelper;

class LeadHelper
{

    public static function tripTypeList(): array
    {
        return [
            Lead::TRIP_TYPE_ONE_WAY => 'One Way',
            Lead::TRIP_TYPE_ROUND_TRIP => 'Round Trip',
            Lead::TRIP_TYPE_MULTI_DESTINATION => 'Multi destination'
        ];
    }

    public static function tripTypeName($type): string
    {
        return ArrayHelper::getValue(self::tripTypeList(), $type);
    }

    public static function cabinList(): array
    {
        return [
            Lead::CABIN_ECONOMY => 'Economy',
            Lead::CABIN_PREMIUM => 'Premium eco',
            Lead::CABIN_BUSINESS => 'Business',
            Lead::CABIN_FIRST => 'First',
        ];
    }

    public static function cabinName($type): string
    {
        return ArrayHelper::getValue(self::cabinList(), $type);
    }

}

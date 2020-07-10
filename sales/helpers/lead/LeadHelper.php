<?php

namespace sales\helpers\lead;

use common\models\Lead;
use common\models\LeadFlightSegment;
use yii\helpers\ArrayHelper;

class LeadHelper
{



    /**
     * @return array
     */
    public static function tripTypeList(): array
    {
        return [
            Lead::TRIP_TYPE_ONE_WAY => 'One Way',
            Lead::TRIP_TYPE_ROUND_TRIP => 'Round Trip',
            Lead::TRIP_TYPE_MULTI_DESTINATION => 'Multi destination'
        ];
    }


    /**
     * @param string|null $type
     * @return string|null
     */
    public static function tripTypeName(?string $type): ?string
    {
        return ArrayHelper::getValue(self::tripTypeList(), $type);
    }

    /**
     * @return array
     */
    public static function cabinList(): array
    {
        return [
            Lead::CABIN_ECONOMY => 'Economy',
            Lead::CABIN_PREMIUM => 'Premium eco',
            Lead::CABIN_BUSINESS => 'Business',
            Lead::CABIN_FIRST => 'First',
        ];
    }


    /**
     * @param string|null $type
     * @return string|null
     */
    public static function cabinName(?string $type): ?string
    {
        return ArrayHelper::getValue(self::cabinList(), $type);
    }

    /**
     * @return array
     */
    public static function statusList(): array
    {
        return [
            Lead::STATUS_PENDING        => 'Pending',
            Lead::STATUS_PROCESSING     => 'Processing',
            Lead::STATUS_REJECT         => 'Reject',
            Lead::STATUS_FOLLOW_UP      => 'Follow Up',
            Lead::STATUS_ON_HOLD        => 'Hold On',
            Lead::STATUS_SOLD           => 'Sold',
            Lead::STATUS_TRASH          => 'Trash',
            Lead::STATUS_BOOKED         => 'Booked',
            Lead::STATUS_SNOOZE         => 'Snooze',
        ];
    }


    /**
     * @param string|null $status
     * @return string|null
     */
    public static function statusName(?string $status): ?string
    {
        return ArrayHelper::getValue(self::statusList(), $status);
    }

    /**
     * @return array
     */
    public static function adultsChildrenInfantsList(): array
    {
        return array_combine(range(0, 9), range(0, 9));
    }

    /**
     * @param Lead $lead
     * @return array
     */
    public static function getAllOriginsByLead(Lead $lead): array
    {
        $result = [];
        foreach ($lead->leadFlightSegments as $segment) {
            $result[] = $segment->origin;
        }
        return $result;
    }

    /**
     * @param Lead $lead
     * @return array
     */
    public static function getAllDestinationByLead(Lead $lead): array
    {
        $result = [];
        foreach ($lead->leadFlightSegments as $segment) {
            $result[] = $segment->destination;
        }
        return $result;
    }

    /**
     * @param Lead $lead
     * @return array
     */
    public static function getAllIataByLead(Lead $lead): array
    {
        $result = [];
        foreach ($lead->leadFlightSegments as $segment) {
            $result[] = $segment->origin;
            $result[] = $segment->destination;
        }
        return $result;
    }
}

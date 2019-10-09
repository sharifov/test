<?php

namespace sales\services\lead\calculator;

use common\models\Lead;
use sales\repositories\airport\AirportRepository;

/**
 * Class LeadTripTypeCalculator
 */
class LeadTripTypeCalculator
{

    public static function calculate(SegmentDTO ...$segments): string
    {
        $countSegments = count($segments);

        if ($countSegments === 0) {
            return '';
        }

        if ($countSegments === 1) {
            return Lead::TRIP_TYPE_ONE_WAY;
        }

        if ($countSegments === 2) {

            reset($segments);

            $segmentOne = current($segments);
            $segmentOneOrigin = $segmentOne->origin;
            $segmentOneDestination = $segmentOne->destination;

            $segmentTwo = next($segments);
            $segmentTwoOrigin = $segmentTwo->origin;
            $segmentTwoDestination = $segmentTwo->destination;

            if ($segmentOneOrigin == $segmentTwoDestination && $segmentOneDestination == $segmentTwoOrigin) {
                return Lead::TRIP_TYPE_ROUND_TRIP;
            }

            $repo = \Yii::createObject(AirportRepository::class);

            if (
                ($segmentOneOrigin = $repo->getByIata($segmentOneOrigin)) &&
                ($segmentOneDestination = $repo->getByIata($segmentOneDestination)) &&
                ($segmentTwoOrigin = $repo->getByIata($segmentTwoOrigin)) &&
                ($segmentTwoDestination = $repo->getByIata($segmentTwoDestination))
            ) {
                if ($segmentOneOrigin->city == $segmentTwoDestination->city && $segmentOneDestination->city == $segmentTwoOrigin->city) {
                    return Lead::TRIP_TYPE_ROUND_TRIP;
                }
            }

        }

        return Lead::TRIP_TYPE_MULTI_DESTINATION;
    }

}

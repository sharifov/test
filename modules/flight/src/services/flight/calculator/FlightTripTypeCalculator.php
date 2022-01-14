<?php

namespace modules\flight\src\services\flight\calculator;

use modules\flight\models\Flight;
use modules\flight\src\dto\flightSegment\SegmentDTO;
use src\repositories\airport\AirportRepository;

class FlightTripTypeCalculator
{
    /**
     * @param SegmentDTO ...$segments
     * @return int|null
     * @throws \yii\base\InvalidConfigException
     */
    public static function calculate(SegmentDTO ...$segments): ?int
    {
        $countSegments = count($segments);

        if ($countSegments === 0) {
            return null;
        }

        if ($countSegments === 1) {
            return Flight::TRIP_TYPE_ONE_WAY;
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
                return Flight::TRIP_TYPE_ROUND_TRIP;
            }

            $repo = \Yii::createObject(AirportRepository::class);

            if (
                ($segmentOneOrigin = $repo->getByIata($segmentOneOrigin)) &&
                ($segmentOneDestination = $repo->getByIata($segmentOneDestination)) &&
                ($segmentTwoOrigin = $repo->getByIata($segmentTwoOrigin)) &&
                ($segmentTwoDestination = $repo->getByIata($segmentTwoDestination))
            ) {
                if ($segmentOneOrigin->city == $segmentTwoDestination->city && $segmentOneDestination->city == $segmentTwoOrigin->city) {
                    return Flight::TRIP_TYPE_ROUND_TRIP;
                }
            }
        }

        return Flight::TRIP_TYPE_MULTI_DESTINATION;
    }
}

<?php

namespace modules\flight\src\entities\flightQuoteTrip\serializer;

use src\entities\serializer\Serializer;

class FlightQuoteTripSerializer extends Serializer
{
    /**
     * @inheritDoc
     */
    public static function fields(): array
    {
        return [
            'fqt_id',
            'fqt_uid',
            'fqt_key',
            'fqt_duration'
        ];
    }

    /**
     * @inheritDoc
     */
    public function getData(): array
    {
        return $this->toArray();
    }
}

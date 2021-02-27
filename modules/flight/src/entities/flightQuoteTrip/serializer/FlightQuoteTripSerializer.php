<?php

namespace modules\flight\src\entities\flightQuoteTrip\serializer;

use sales\entities\serializer\Serializer;

class FlightQuoteTripSerializer extends Serializer
{

    /**
     * @inheritDoc
     */
    public static function fields(): array
    {
        return [
            'fqt_id',
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

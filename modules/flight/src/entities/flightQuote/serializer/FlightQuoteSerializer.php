<?php

namespace modules\flight\src\entities\flightQuote\serializer;

use modules\flight\models\FlightQuote;
use sales\entities\serializer\Serializer;

/**
 * Class FlightQuoteSerializer
 *
 * @property FlightQuote $model
 */
class FlightQuoteSerializer extends Serializer
{
    public function __construct(FlightQuote $model)
    {
        parent::__construct($model);
    }

    public static function fields(): array
    {
        return [
            'fq_flight_id'
        ];
    }

    public function getData(): array
    {
        return $this->toArray();
    }
}

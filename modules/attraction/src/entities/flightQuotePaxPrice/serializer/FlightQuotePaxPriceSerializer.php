<?php

namespace modules\flight\src\entities\flightQuotePaxPrice\serializer;

use modules\flight\models\FlightQuotePaxPrice;
use sales\entities\serializer\Serializer;

/**
 * Class FlightQuotePaxPriceSerializer
 *
 * @property FlightQuotePaxPrice $model
 */
class FlightQuotePaxPriceSerializer extends Serializer
{
    public function __construct(FlightQuotePaxPrice $model)
    {
        parent::__construct($model);
    }

    public static function fields(): array
    {
        return [
            'qpp_fare',
            'qpp_tax',
            'qpp_system_mark_up',
            'qpp_agent_mark_up',
            'qpp_origin_fare',
            'qpp_origin_currency',
            'qpp_origin_tax',
            'qpp_client_currency',
            'qpp_client_fare',
            'qpp_client_tax',
        ];
    }

    public function getData(): array
    {
        return $this->toArray();
    }
}

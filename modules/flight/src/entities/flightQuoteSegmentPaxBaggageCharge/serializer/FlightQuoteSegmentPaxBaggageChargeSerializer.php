<?php

namespace modules\flight\src\entities\flightQuoteSegmentPaxBaggageCharge\serializer;

use modules\flight\models\FlightQuoteSegmentPaxBaggageCharge;
use sales\entities\serializer\Serializer;

/**
 * Class FlightQuoteSegmentPaxBaggageChargeSerializer
 *
 * @property FlightQuoteSegmentPaxBaggageCharge $model
 */
class FlightQuoteSegmentPaxBaggageChargeSerializer extends Serializer
{
    public function __construct(FlightQuoteSegmentPaxBaggageCharge $model)
    {
        parent::__construct($model);
    }

    public static function fields(): array
    {
        return [
            'qsbc_first_piece',
            'qsbc_last_piece',
            'qsbc_origin_price',
            'qsbc_origin_currency',
            'qsbc_price',
            'qsbc_client_price',
            'qsbc_client_currency',
            'qsbc_max_weight',
            'qsbc_max_size',
        ];
    }

    public function getData(): array
    {
        return $this->toArray();
    }
}

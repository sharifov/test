<?php

namespace modules\flight\src\entities\flightPax\serializer;

use sales\entities\serializer\Serializer;

class FlightPaxSerializer extends Serializer
{

    /**
     * @inheritDoc
     */
    public static function fields(): array
    {
        return [
            'fp_uid',
            'fp_pax_id',
            'fp_pax_type',
            'fp_first_name',
            'fp_last_name',
            'fp_middle_name',
            'fp_dob'
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

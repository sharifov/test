<?php

namespace modules\cruise\src\entity\cruise\serializer;

use modules\cruise\src\entity\cruise\Cruise;
use sales\entities\serializer\Serializer;

/**
 * Class CruiseSerializer
 *
 * @property Cruise $model
 */
class CruiseSerializer extends Serializer
{
    public function __construct(Cruise $model)
    {
        parent::__construct($model);
    }

    public static function fields(): array
    {
        return [
            'crs_product_id',
            'crs_departure_date_from',
            'crs_arrival_date_to',
            'crs_destination_code',
            'crs_destination_label',
        ];
    }

    public function getData(): array
    {
        return $this->toArray();
    }
}

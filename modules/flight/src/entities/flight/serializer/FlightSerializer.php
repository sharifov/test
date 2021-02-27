<?php

namespace modules\flight\src\entities\flight\serializer;

use modules\flight\models\Flight;
use sales\entities\serializer\Serializer;

/**
 * Class FlightSerializer
 *
 * @property Flight $model
 */
class FlightSerializer extends Serializer
{
    public function __construct(Flight $model)
    {
        parent::__construct($model);
    }

    public static function fields(): array
    {
        return [
            'fl_product_id',
            'fl_trip_type_id',
            'fl_cabin_class',
            'fl_adults',
            'fl_children',
            'fl_infants'
        ];
    }

    public function getData(): array
    {
        $data = $this->toArray();
        $data['fl_trip_type_name'] = $this->model->getTripTypeName();
        $data['fl_cabin_class_name'] = $this->model->getCabinClassName();
        return $data;
    }
}

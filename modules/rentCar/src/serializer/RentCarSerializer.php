<?php

namespace modules\rentCar\src\serializer;

use common\models\Airports;
use modules\rentCar\src\entity\rentCar\RentCar;
use sales\entities\serializer\Serializer;

/**
 * Class RentCarSerializer
 *
 * @property RentCar $model
 */
class RentCarSerializer extends Serializer
{
    public function __construct(RentCar $model)
    {
        parent::__construct($model);
    }

    public static function fields(): array
    {
        return [
            'prc_product_id',
            'prc_pick_up_code',
            'prc_drop_off_code',
            'prc_pick_up_date',
            'prc_drop_off_date',
            'prc_pick_up_time',
            'prc_drop_off_time',
        ];
    }

    public function getData(): array
    {
        $data = $this->toArray();

        $data['pickUpLocation'] = $this->model->prc_pick_up_code ? Airports::getCityByIata($this->model->prc_pick_up_code) : '';
        $data['dropOffLocation'] = $this->model->prc_drop_off_code ? Airports::getCityByIata($this->model->prc_drop_off_code) : '';

        return $data;
    }
}

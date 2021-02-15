<?php

namespace modules\rentCar\src\serializer;

use modules\rentCar\src\entity\rentCar\RentCar;
use modules\rentCar\src\entity\rentCarQuote\RentCarQuote;
use sales\entities\serializer\Serializer;

/**
 * Class RentCarQuoteSerializer
 *
 * @property RentCar $model
 */
class RentCarQuoteSerializer extends Serializer
{
    public function __construct(RentCarQuote $model)
    {
        parent::__construct($model);
    }

    public static function fields(): array
    {
        return [
            'rcq_model_name',
            'rcq_category',
            'rcq_vendor_name',
            'rcq_price_per_day',
            'rcq_currency',
            'rcq_options',
            'rcq_advantages',
            'rcq_image_url',
            'rcq_vendor_logo_url',
        ];
    }

    public function getData(): array
    {
        $data =  $this->toArray();

        if ($this->model->rcqRentCar) {
            $data['search_request'] = $this->model->rcqRentCar->serialize();
        }

        return $data;
    }
}

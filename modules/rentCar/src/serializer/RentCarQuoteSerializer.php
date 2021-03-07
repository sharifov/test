<?php

namespace modules\rentCar\src\serializer;

use modules\rentCar\src\entity\rentCarQuote\RentCarQuote;
use sales\entities\serializer\Serializer;

/**
 * Class RentCarQuoteSerializer
 *
 * @property RentCarQuote $model
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
            'rcq_options',
            'rcq_advantages',
            'rcq_image_url',
            'rcq_vendor_logo_url',
            'rcq_json_response',
            'rcq_booking_id',
            'rcq_booking_json',
        ];
    }

    public function getData(): array
    {
        $data =  $this->toArray();
        if ($this->model->rcqRentCar) {
            $data['rent_car'] = $this->model->rcqRentCar->serialize();
        }
        $data['project_key'] = $this->model->rcqProductQuote->pqProduct->prLead->project->project_key;

        $client = $this->model->rcqRentCar->prcProduct->prLead->client;
        $data['client']['first_name'] = $client->first_name;
        $data['client']['last_name'] = $client->last_name;

        return $data;
    }
}

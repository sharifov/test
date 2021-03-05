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
        ];
    }

    public function getData(): array
    {
        $data =  $this->toArray();
        if ($this->model->rcqRentCar) {
            $data['search_request'] = $this->model->rcqRentCar->serialize();
        }
        $data['project_key'] = $this->model->rcqProductQuote->pqProduct->prLead->project->project_key;
        return $data;
    }
}

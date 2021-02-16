<?php

namespace modules\attraction\src\serializer;

use modules\attraction\models\AttractionQuote;
use sales\entities\serializer\Serializer;

/**
 * Class RentCarQuoteSerializer
 *
 * @property AttractionQuote $model
 */
class AttractionQuoteSerializer extends Serializer
{
    public function __construct(AttractionQuote $model)
    {
        parent::__construct($model);
    }

    public static function fields(): array
    {
        return [
            'atnq_booking_id',
            'atnq_attraction_name',
            'atnq_supplier_name',
            'atnq_type',
            'atnq_json_response',
        ];
    }

    public function getData(): array
    {
        $data =  $this->toArray();

        if ($this->model->atnqAttraction) {
            $data['search_request'] = $this->model->atnqAttraction->serialize();
        }

        return $data;
    }
}

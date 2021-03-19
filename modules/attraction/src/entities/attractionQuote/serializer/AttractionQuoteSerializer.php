<?php

namespace modules\attraction\src\entities\attractionQuote\serializer;

use modules\attraction\models\AttractionQuote;
use sales\entities\serializer\Serializer;

/**
 * Class AttractionQuoteSerializer
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
            'atnq_type',
            'atnq_json_response',
            'atnq_product_details_json',
            'atnq_availability_date'
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

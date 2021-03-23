<?php

namespace modules\attraction\src\entities\attraction\serializer;

use modules\attraction\models\Attraction;
use sales\entities\serializer\Serializer;

/**
 * Class AttractionSerializer
 *
 * @property Attraction $model
 */
class AttractionSerializer extends Serializer
{
    public function __construct(Attraction $model)
    {
        parent::__construct($model);
    }

    public static function fields(): array
    {
        return [
            'atn_product_id',
            'atn_date_from',
            'atn_date_to',
            'atn_destination',
            'atn_destination_code',
        ];
    }

    public function getData(): array
    {
        $data = $this->toArray();

        if ($this->model->attractionPaxes) {
            foreach ($this->model->attractionPaxes as $traveler) {
                $data['travelers'][] = $traveler->serialize();
            }
        }
        return $data;
    }
}

<?php

namespace modules\attraction\src\entities\attractionPax\serializer;

use modules\attraction\models\AttractionPax;
use sales\entities\serializer\Serializer;

/**
 * Class AttractionPaxSerializer
 *
 * @property AttractionPax $model
 */

class AttractionPaxSerializer extends Serializer
{
    public function __construct(AttractionPax $model)
    {
        parent::__construct($model);
    }

    public static function fields(): array
    {
        return [
            'atnp_atn_id',
            'atnp_type_id',
            'atnp_age',
            'atnp_first_name',
            'atnp_last_name',
            'atnp_dob'
        ];
    }

    public function getData(): array
    {
        $data = $this->toArray();
        $data['atnp_type_name'] = AttractionPax::PAX_LIST[$this->model->atnp_type_id];
        return $data;
    }
}

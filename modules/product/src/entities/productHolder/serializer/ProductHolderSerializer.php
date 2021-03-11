<?php

namespace modules\product\src\entities\productHolder\serializer;

use sales\entities\serializer\Serializer;

class ProductHolderSerializer extends Serializer
{
    /**
     * @inheritDoc
     */
    public static function fields(): array
    {
        return [
            'ph_first_name',
            'ph_last_name',
            'ph_email',
            'ph_phone_number'
        ];
    }

    /**
     * @inheritDoc
     */
    public function getData(): array
    {
        return $this->toArray();
    }
}

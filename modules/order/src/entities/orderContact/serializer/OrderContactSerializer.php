<?php

namespace modules\order\src\entities\orderContact\serializer;

use modules\order\src\entities\orderContact\OrderContact;
use sales\entities\serializer\Serializer;

class OrderContactSerializer extends Serializer
{
    public function __construct(OrderContact $model)
    {
        parent::__construct($model);
    }

    /**
     * @inheritDoc
     */
    public static function fields(): array
    {
        return [
            'oc_first_name',
            'oc_last_name',
            'oc_middle_name',
            'oc_email',
            'oc_phone_number',
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

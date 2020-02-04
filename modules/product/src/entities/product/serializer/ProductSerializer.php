<?php

namespace modules\product\src\entities\product\serializer;

use modules\product\src\entities\product\Product;
use sales\entities\serializer\Serializer;

/**
 * Class ProductExtraData
 *
 * @property Product $model
 */
class ProductSerializer extends Serializer
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public static function fields(): array
    {
        return [
            //'pr_id',
            'pr_type_id',
            'pr_name',
            'pr_lead_id',
            'pr_description',
            'pr_status_id',
            'pr_service_fee_percent',
//            'pr_created_user_id',
//            'pr_updated_user_id',
//            'pr_created_dt',
//            'pr_updated_dt',
        ];
    }

    public function getData(): array
    {
        return $this->toArray();
    }
}

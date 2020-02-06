<?php

namespace modules\product\src\entities\productQuoteOption\serializer;

use modules\product\src\entities\productQuoteOption\ProductQuoteOption;
use sales\entities\serializer\Serializer;

/**
 * Class ProductQuoteOptionExtraData
 *
 * @property ProductQuoteOption $model
 */
class ProductQuoteOptionSerializer extends Serializer
{
    public function __construct(ProductQuoteOption $model)
    {
        parent::__construct($model);
    }

    public static function fields(): array
    {
        return [
            //'pqo_id',
            //'pqo_product_quote_id',
            //'pqo_product_option_id',
            'pqo_name',
            'pqo_description',
            'pqo_status_id',
            'pqo_price',
            'pqo_client_price',
            'pqo_extra_markup',
            //'pqo_created_user_id',
            //'pqo_updated_user_id',
            //'pqo_created_dt',
            //'pqo_updated_dt',
        ];
    }

    public function getData(): array
    {
        return $this->toArray();
    }
}

<?php

namespace modules\product\src\helpers\formatters;

use modules\product\src\entities\productQuote\ProductQuote;
use yii\bootstrap4\Html;

class ProductQuoteFormatter
{
    public static function asProductQuote(ProductQuote $productQuote): string
    {
        return Html::a(
            'quote: ' . $productQuote->pq_id,
            ['/product/product-quote-crud/view', 'id' => $productQuote->pq_id],
            ['target' => '_blank', 'data-pjax' => 0]
        );
    }
}

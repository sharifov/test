<?php

namespace modules\product\src\helpers\formatters;

use modules\product\src\entities\product\Product;
use yii\bootstrap4\Html;

class ProductFormatter
{
    public static function asProduct(Product $product): string
    {
        return Html::a(
            'product: ' . $product->pr_id,
            ['/product/product-crud/view', 'id' => $product->pr_id],
            ['target' => '_blank', 'data-pjax' => 0]
        );
    }
}

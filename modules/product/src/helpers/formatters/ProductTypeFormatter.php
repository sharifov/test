<?php

namespace modules\product\src\helpers\formatters;

use modules\product\src\entities\productType\ProductType;
use modules\product\src\entities\productType\ProductTypeQuery;
use yii\bootstrap4\Html;

class ProductTypeFormatter
{
    public static function asProductType(int $value): string
    {
        switch ($value) {
            case ProductType::PRODUCT_FLIGHT:
                $class = 'info';
                break;
            case ProductType::PRODUCT_HOTEL:
                $class = 'warning';
                break;
            default:
                $class = 'default';
        }

        return Html::tag('span', ProductTypeQuery::getListAll()[$value] ?? 'Undefined', ['class' => 'badge badge-' . $class]);
    }
}

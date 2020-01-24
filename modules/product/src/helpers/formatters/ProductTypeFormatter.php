<?php

namespace modules\product\src\helpers\formatters;

use common\models\ProductType;
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

        return Html::tag('span', ProductType::getListAll()[$value] ?? 'Undefined', ['class' => 'label label-' . $class]);
    }
}

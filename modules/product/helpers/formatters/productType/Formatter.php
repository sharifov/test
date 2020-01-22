<?php

namespace modules\product\helpers\formatters\productType;

use common\models\ProductType;
use yii\bootstrap4\Html;

class Formatter
{
    public static function asProductType(int $value): string
    {
        switch ($value) {
            case ProductType::PRODUCT_FLIGHT:
                $class = 'label label-info';
                break;
            case ProductType::PRODUCT_HOTEL:
                $class = 'label label-warning';
                break;
            default:
                $class = 'label label-default';
        }

        return Html::tag('span', ProductType::getListAll()[$value] ?? 'Undefined', ['class' => $class]);
    }
}

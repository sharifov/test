<?php

namespace modules\order\src\helpers\formatters;

use modules\order\src\entities\order\Order;
use yii\bootstrap4\Html;

class OrderFormatter
{
    public static function asOrder(Order $order): string
    {
        return Html::a(
            'order: ' . $order->or_id,
            ['/order/order-crud/view', 'id' => $order->or_id],
            ['target' => '_blank', 'data-pjax' => 0]
        );
    }
}

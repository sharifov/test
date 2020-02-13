<?php

namespace modules\order\src\grid\columns;

use modules\order\src\entities\order\OrderStatus;
use yii\grid\DataColumn;

/**
 * Class OrderStatusColumn
 *
 * Ex.
        [
            'class' => \modules\order\src\grid\columns\OrderStatusColumn::class,
            'attribute' => 'status_id',
        ],
 */
class OrderStatusColumn extends DataColumn
{
    public $format = 'orderStatus';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = OrderStatus::getList();
        }
    }
}

<?php

namespace modules\order\src\grid\columns;

use modules\order\src\entities\order\OrderStatusAction;
use yii\grid\DataColumn;

/**
 * Class OrderStatusActionColumn
 *
 * Ex.
        [
            'class' => \modules\order\src\grid\columns\OrderStatusActionColumn::class,
            'attribute' => 'action_id'
        ],
 */
class OrderStatusActionColumn extends DataColumn
{
    public $format = 'orderStatusAction';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = OrderStatusAction::getList();
        }
    }
}

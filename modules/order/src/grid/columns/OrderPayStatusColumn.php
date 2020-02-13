<?php

namespace modules\order\src\grid\columns;

use modules\order\src\entities\order\OrderPayStatus;
use yii\grid\DataColumn;

/**
 * Class OrderPayStatusColumn
 *
 * Ex.
        [
            'class' => \modules\order\src\grid\columns\OrderPayStatusColumn::class,
            'attribute' => 'status_id',
        ],
 */
class OrderPayStatusColumn extends DataColumn
{
    public $format = 'orderPayStatus';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = OrderPayStatus::getList();
        }
    }
}

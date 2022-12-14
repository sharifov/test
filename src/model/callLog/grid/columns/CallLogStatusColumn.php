<?php

namespace src\model\callLog\grid\columns;

use src\model\callLog\entity\callLog\CallLogStatus;
use yii\grid\DataColumn;

/**
 * Class CallLogTypeColumn
 *
 * Ex.
        [
            'class' => \src\model\callLog\grid\columns\CallLogStatusColumn::class,
            'attribute' => 'cl_status_id',
        ],
 */
class CallLogStatusColumn extends DataColumn
{
    public $format = 'callLogStatus';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = CallLogStatus::getList();
        }

        if (!$this->attribute) {
            $this->attribute = 'cl_status_id';
        }
    }
}

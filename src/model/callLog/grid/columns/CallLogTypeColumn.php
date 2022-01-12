<?php

namespace src\model\callLog\grid\columns;

use src\model\callLog\entity\callLog\CallLogType;
use yii\grid\DataColumn;

/**
 * Class CallLogTypeColumn
 *
 * Ex.
        [
            'class' => \src\model\callLog\grid\columns\CallLogTypeColumn::class,
            'attribute' => 'cl_type_id',
        ],
 */
class CallLogTypeColumn extends DataColumn
{
    public $format = 'callLogType';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = CallLogType::getList();
        }

        if (!$this->attribute) {
            $this->attribute = 'cl_type_id';
        }
    }
}

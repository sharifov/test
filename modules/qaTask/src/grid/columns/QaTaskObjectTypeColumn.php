<?php

namespace modules\qaTask\src\grid\columns;

use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use yii\grid\DataColumn;

/**
 * Class QaTaskObjectTypeColumn
 *
 * Ex.
    [
        'class' => \modules\qaTask\src\grid\columns\QaTaskObjectTypeColumn::class,
        'attribute' => 'tc_object_type_id',
    ],
 */
class QaTaskObjectTypeColumn extends DataColumn
{
    public $format = 'qaTaskObjectType';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = QaTaskObjectType::getList();
        }
    }
}

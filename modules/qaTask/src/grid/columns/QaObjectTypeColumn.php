<?php

namespace modules\qaTask\src\grid\columns;

use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use yii\grid\DataColumn;

/**
 * Class QaObjectTypeColumn
 *
 * Ex.
    [
        'class' => \modules\qaTask\src\grid\columns\QaObjectTypeColumn::class,
        'attribute' => 'tc_object_type_id',
    ],
 */
class QaObjectTypeColumn extends DataColumn
{
    public $format = 'qaObjectType';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = QaTaskObjectType::getList();
        }
    }
}

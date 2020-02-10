<?php

namespace modules\qaTask\src\grid\columns;
use modules\qaTask\src\entities\qaTask\QaTaskCreatedType;
use yii\grid\DataColumn;

/**
 * Class QaTaskCreatedTypeColumn
 *
 * Ex.
     [
         'class' => \modules\qaTask\src\grid\columns\QaTaskCreatedTypeColumn::class,
         'attribute' => 'created_type_id',
     ],
 */
class QaTaskCreatedTypeColumn extends DataColumn
{
    public $format = 'qaTaskCreatedType';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = QaTaskCreatedType::getList();
        }
    }
}

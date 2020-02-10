<?php

namespace modules\qaTask\src\grid\columns;

use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use yii\grid\DataColumn;

/**
 * Class QaTaskStatusColumn
 *
 * Ex.
     [
         'class' => \modules\qaTask\src\grid\columns\QaTaskStatusColumn::class,
         'attribute' => 'status_id',
     ],
 */
class QaTaskStatusColumn extends DataColumn
{
    public $format = 'qaTaskStatus';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = QaTaskStatus::getList();
        }
    }
}

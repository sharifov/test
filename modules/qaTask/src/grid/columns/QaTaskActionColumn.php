<?php

namespace modules\qaTask\src\grid\columns;

use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use yii\grid\DataColumn;

/**
 * Class QaTaskActionColumn
 *
 * Ex.
     [
         'class' => \modules\qaTask\src\grid\columns\QaTaskActionColumn::class,
         'attribute' => 'tsl_action_id',
     ],
 */
class QaTaskActionColumn extends DataColumn
{
    public $format = 'qaTaskAction';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = QaTaskActions::getList();
        }
    }
}

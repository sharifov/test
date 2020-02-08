<?php

namespace modules\qaTask\src\grid\columns;

use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatusAction;
use yii\grid\DataColumn;

/**
 * Class QaTaskStatusActionColumn
 *
 * Ex.
     [
         'class' => \modules\qaTask\src\grid\columns\QaTaskStatusActionColumn::class,
         'attribute' => 'tsl_action_id',
     ],
 */
class QaTaskStatusActionColumn extends DataColumn
{
    public $format = 'qaTaskStatusAction';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = QaTaskStatusAction::getList();
        }
    }
}


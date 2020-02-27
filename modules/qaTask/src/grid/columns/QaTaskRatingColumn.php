<?php

namespace modules\qaTask\src\grid\columns;

use modules\qaTask\src\entities\qaTask\QaTaskRating;
use yii\grid\DataColumn;

/**
 * Class QaTaskRatingColumn
 *
 * Ex.
    [
        'class' => \modules\qaTask\src\grid\columns\QaTaskRatingColumn::class,
        'attribute' => 'rating',
    ],
 */
class QaTaskRatingColumn extends DataColumn
{
    public $format = 'qaTaskRating';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = QaTaskRating::getList();
        }
    }
}

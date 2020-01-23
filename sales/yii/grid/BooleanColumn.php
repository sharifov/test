<?php

namespace sales\yii\grid;

use yii\grid\DataColumn;

/**
 * Class BooleanColumn
 *
 *  Ex.
    [
        'class' => \sales\yii\grid\BooleanColumn::class,
        'attribute' => 'ugs_enabled',
    ],
 *
 */
class BooleanColumn extends DataColumn
{
    public $format = 'booleanByLabel';
    public $filter = [
        1 => 'Yes',
        0 => 'No'
    ];
}

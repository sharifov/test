<?php

namespace sales\yii\grid;

use yii\grid\DataColumn;

class BooleanColumn extends DataColumn
{
    public $format = 'booleanByLabel';
    public $filter = [
        1 => 'Yes',
        0 => 'No'
    ];
}

<?php

namespace common\components\grid;

use src\helpers\DateHelper;
use yii\grid\DataColumn;

class MonthColumn extends DataColumn
{
    public $format = 'MonthNameByMonthNumber';

    public function init()
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = DateHelper::getMonthList();
        }
    }
}

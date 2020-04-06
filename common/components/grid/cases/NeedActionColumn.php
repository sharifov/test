<?php

namespace common\components\grid\cases;

use yii\bootstrap4\Html;
use yii\grid\DataColumn;

/**
 * Class BooleanColumn
 *
 *  Ex.
    [
        'class' => \common\components\grid\cases\NeedActionColumn::class,
        'attribute' => 'cs_need_action',
    ],
 *
 */
class NeedActionColumn extends DataColumn
{
    public $filter = [
        1 => 'Yes',
        0 => 'No'
    ];

    protected function renderDataCellContent($model, $key, $index): string
    {
        if ($model->{$this->attribute} === null) {
            return '';
        }
        if ($model->{$this->attribute}) {
                return Html::tag('span', 'Yes', ['class' => 'badge badge-danger']);
        }
        return Html::tag('span', 'No', ['class' => 'badge badge-light']);
    }
}

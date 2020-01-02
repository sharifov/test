<?php

namespace sales\yii\grid;

use dosamigos\datepicker\DatePicker;
use yii\grid\DataColumn;

/**
 * Class DateTimeColumn
 *
 * Ex.
    [
        'class' => \sales\yii\grid\DateTimeColumn::class,
        'attribute' => 'pbl_updated_dt',
    ],
 *
 */
class DateTimeColumn extends DataColumn
{
    public $format = 'byUserDateTime';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = DatePicker::widget([
                'model' => $this->grid->filterModel,
                'attribute' => $this->attribute,
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                ],
                'options' => [
                    'autocomplete' => 'off',
                    'placeholder' => 'Choose Date'
                ],
            ]);
        }
    }
}

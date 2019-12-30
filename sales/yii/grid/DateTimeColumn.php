<?php

namespace sales\yii\grid;

use dosamigos\datepicker\DatePicker;
use yii\grid\DataColumn;

class DateTimeColumn extends DataColumn
{
    public $format = 'dateTimeByUserDt';

    public function init(): void
    {
        parent::init();
        if (!$this->filter) {
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

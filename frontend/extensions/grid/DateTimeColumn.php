<?php

namespace frontend\extensions\grid;

use dosamigos\datepicker\DatePicker;
use yii\grid\DataColumn;

class DateTimeColumn extends DataColumn
{
    public $searchModel;
    public $format = 'dateTimeByUserDt';

    public function init(): void
    {
        parent::init();
        $this->filter = DatePicker::widget([
            'model' => $this->searchModel,
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

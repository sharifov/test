<?php

namespace common\components\grid;

use dosamigos\datepicker\DatePicker;
use yii\grid\DataColumn;

/**
 * Class DateTimeColumn
 *
 * Ex.
 * [
 * 'class' => \common\components\grid\DateTimeColumn::class,
 * 'attribute' => 'pbl_updated_dt',
 * ],
 */
class DateTimeColumn extends DataColumn
{
    public $format = 'byUserDateTime';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null && $this->grid->filterModel !== false) {
            $this->filter = DatePicker::widget([
                'model' => $this->grid->filterModel,
                'attribute' => $this->attribute,
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                    'clearBtn' => true,
                    'startDate' => date('Y-m-d', strtotime('2017-01-01')),
                    'endDate' => date('Y-m-d', time())
                ],
                'options' => [
                    'autocomplete' => 'off',
                    'placeholder' => 'Choose Date',
                    'readonly' => 'readonly'
                ],
                'containerOptions' => [
                    'class' => (array_key_exists($this->attribute, $this->grid->filterModel->errors)) ? 'has-error' : null,
                ],
                'clientEvents' => [
                    'clearDate' => 'function (e) {$(e.target).find("input").change();}',
                ],
            ]);
        }
    }
}

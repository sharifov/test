<?php

namespace common\components\grid;

use dosamigos\datepicker\DatePicker;
use yii\grid\DataColumn;

/**
 * Class DateColumn
 *
 * Ex.
 * [
 * 'class' => \common\components\grid\DateColumn::class,
 * 'attribute' => 'pbl_updated_dt',
 * ],
 */
class DateColumn extends DataColumn
{
    public $format = 'byUserDate';

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

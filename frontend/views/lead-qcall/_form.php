<?php

use borales\extensions\phoneInput\PhoneInput;
use dosamigos\datetimepicker\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LeadQcall */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-qcall-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="col-md-4">
        <?= $form->field($model, 'lqc_lead_id')->input('number', ['min' => 1]) ?>

        <?= $form->field($model, 'lqc_created_dt')
            ->widget(DateTimePicker::class, [
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd hh:ii:ss',
                    'todayBtn' => false,
                    'minViewMode' => 1
                ],
                'options' => [
                    'autocomplete' => 'off',
                    'placeholder' => 'Choose Date'
                ],
            ]) ?>

        <?= $form->field($model, 'lqc_dt_from')
            ->widget(DateTimePicker::class, [
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd hh:ii:ss',
                    'todayBtn' => false,
                    'minViewMode' => 1
                ],
                'options' => [
                    'autocomplete' => 'off',
                    'placeholder' => 'Choose Date'
                ],
            ]) ?>

        <?= $form->field($model, 'lqc_dt_to')
            ->widget(DateTimePicker::class, [
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd hh:ii:ss',
                    'todayBtn' => false,
                    'minViewMode' => 1
                ],
                'options' => [
                    'autocomplete' => 'off',
                    'placeholder' => 'Choose Date'
                ],
            ]) ?>

        <?= $form->field($model, 'lqc_reservation_time')
            ->widget(DateTimePicker::class, [
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd hh:ii:ss',
                    'todayBtn' => false,
                    'minViewMode' => 1
                ],
                'options' => [
                    'autocomplete' => 'off',
                    'placeholder' => 'Choose Date'
                ],
            ]) ?>

        <?= $form->field($model, 'lqc_reservation_user_id')->input('number', ['min' => 0]) ?>

        <?= $form->field($model, 'lqc_weight')->input('number', ['min' => 0]) ?>

        <?= $form->field($model, 'lqc_call_from')->widget(PhoneInput::class, [
            'jsOptions' => [
                'formatOnDisplay' => false,
                'autoPlaceholder' => 'off',
                'customPlaceholder' => '',
                'allowDropdown' => false,
                'preferredCountries' => ['us'],
            ]
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

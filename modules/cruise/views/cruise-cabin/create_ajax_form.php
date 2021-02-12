<?php

use modules\cruise\src\entity\cruiseCabinPax\CruiseCabinPax;
use modules\cruise\src\useCase\createCabin\CreateCabinForm;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model CreateCabinForm */

$pjaxId = 'pjax-cruise-cabin-form';
?>

<div class="cruise-cabin-form">

    <script>
        pjaxOffFormSubmit('#<?=$pjaxId?>');
    </script>
    <?php \yii\widgets\Pjax::begin(['id' => $pjaxId, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
        <?php
        $form = ActiveForm::begin([
            'options' => ['data-pjax' => true],
            'action' => ['/cruise/cruise-cabin/create-ajax', 'id' => $model->crc_cruise_id],
            'method' => 'post'
        ]);
        ?>

        <?= $form->field($model, 'crc_cruise_id')->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'crc_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'crc_pax_list')->widget(\unclead\multipleinput\MultipleInput::class, [
            'max'               => 10,
            'min'               => 1, // should be at least 2 rows
            'allowEmptyList'    => false,
            'enableGuessTitle'  => true,
            'enableError'       => true,
            'showGeneralError'  => true,
            'columns' => [
                /*[
                    'name' => 'phone',
                    'title' => 'Phone',
                    'type' => PhoneInput::class,
                    'options' => [
                        'jsOptions' => [
                            'nationalMode' => false,
                            'preferredCountries' => ['us'],
                        ],
                        'options' => [
                            'onkeydown' => '
                                        return !validationField.validate(event);
                                    ',
                            'onkeyup' => '
                                        var value = $(this).val();
                                        $(this).val(value.replace(/[^0-9\+]+/g, ""));
                                    '
                        ]
                    ]
                ],*/
                [
                    'name' => 'crp_id',
                    'type' =>  \unclead\multipleinput\MultipleInputColumn::TYPE_HIDDEN_INPUT,
                    'value' => static function ($data) {
                        return $data['crp_id'] ?? '';
                    },
                ],
                [

                    'name' => 'crp_type_id',
                    'type' => \unclead\multipleinput\MultipleInputColumn::TYPE_DROPDOWN,
                    'title' => 'Pax type',
                    'value' => static function ($data) {
                        return $data['crp_type_id'] ?? '';
                    },
                    'items' => \yii\helpers\ArrayHelper::merge(['' => '---'], CruiseCabinPax::getPaxTypeList()),
                    'headerOptions' => [
                        'style' => 'width: 100px;',
                    ]
                ],
                [
                    'name' => 'crp_age',
                    'type' =>  \unclead\multipleinput\MultipleInputColumn::TYPE_TEXT_INPUT,
                    'title' => 'Age',
                    'value' => static function ($data) {
                        return $data['crp_age'] ?? '';
                    },
                    'headerOptions' => [
                        //'style' => 'width: 80px;',
                    ],
                    'options' => [
                        'class' => 'form-control input_crp_age',
                    ]
                ],

                [
                    'name'  => 'crp_dob',
                    'type'  => \dosamigos\datepicker\DatePicker::class,
                    'title' => 'Date of birth',
                    'value' => static function ($data) {
                        return $data['crp_dob'] ?? '';
                    },
                    'options' => [
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd',
                        ],
                        'options' => [
                            'autocomplete' => 'off',
                            'placeholder' => 'Choose Date'
                        ],
                        'clientEvents' => [
                            'change' => 'function (e, elem) {
                                    let selectedDate = $(e.target).val();
                                    
                                    let diff_ms = Date.now() - new Date(selectedDate);
                                    let age_dt = new Date(diff_ms); 
                                    let age = age_dt.getUTCFullYear() - 1970;
                                    
                                    if (age >= 0) {
                                        $(e.target).closest("tr").find(".input_crp_age").val(age == 0 ? ++age : age);
                                    }
                                }'
                        ]
                    ]
                    //'defaultValue' => date('d-m-Y h:i')
                ],



            ]
        ])->label(false) ?>

        <div class="form-group text-center">
            <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    <?php \yii\widgets\Pjax::end(); ?>

</div>

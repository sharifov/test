<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\forms\HotelRoomForm */
/* @var $form yii\bootstrap4\ActiveForm */

$pjaxId = 'pjax-hotel-room-form';
?>

<div class="hotel-room-form">

    <script>
        pjaxOffFormSubmit('#<?=$pjaxId?>');
    </script>
    <?php \yii\widgets\Pjax::begin(['id' => $pjaxId, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
        <?php
        $form = ActiveForm::begin([
            'options' => ['data-pjax' => true],
            'action' => ['/hotel/hotel-room/create-ajax', 'id' => $model->hr_hotel_id],
            'method' => 'post'
        ]);
        ?>

        <?= $form->field($model, 'hr_hotel_id')->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'hr_room_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'hr_pax_list')->widget(\unclead\multipleinput\MultipleInput::class, [
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
                    'name' => 'hrp_id',
                    'type' =>  \unclead\multipleinput\MultipleInputColumn::TYPE_HIDDEN_INPUT,
                    'value' => static function ($data) {
                        return $data['hrp_id'];
                    },
                ],
                [

                    'name' => 'hrp_type_id',
                    'type' => \unclead\multipleinput\MultipleInputColumn::TYPE_DROPDOWN,
                    'title' => 'Pax type',
                    'value' => static function ($data) {
                        return $data['hrp_type_id'];
                    },
                    'items' => \yii\helpers\ArrayHelper::merge(['' => '---'], \modules\hotel\models\HotelRoomPax::getPaxTypeList()),
                    'headerOptions' => [
                        'style' => 'width: 100px;',
                    ]
                ],
                [
                    'name' => 'hrp_age',
                    'type' =>  \unclead\multipleinput\MultipleInputColumn::TYPE_TEXT_INPUT,
                    'title' => 'Age',
                    'value' => static function ($data) {
                        return $data['hrp_age'];
                    },
                    //'items' => \modules\hotel\models\HotelRoomPax::getPaxTypeList(),
                    'headerOptions' => [
                        //'style' => 'width: 80px;',
                    ],
                    'options' => [
                        'class' => 'form-control input_hrp_age',
                    ]
                ],

                [
                    'name'  => 'hrp_dob',
                    'type'  => \dosamigos\datepicker\DatePicker::class,
                    'title' => 'Date of birth',
                    'value' => static function ($data) {
                        return $data['hrp_dob'];
                    },
                    'options' => [
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd',
                        ],
                        'options' => [
                            'autocomplete' => 'off',
                            'placeholder' =>'Choose Date'
                        ],
                        'clientEvents' => [
                            'change' => 'function (e, elem) {
                                    let selectedDate = $(e.target).val();
                                    
                                    let diff_ms = Date.now() - new Date(selectedDate);
                                    let age_dt = new Date(diff_ms); 
                                    let age = age_dt.getUTCFullYear() - 1970;
                                    
                                    if (age >= 0) {
                                        $(e.target).closest("tr").find(".input_hrp_age").val(age);
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

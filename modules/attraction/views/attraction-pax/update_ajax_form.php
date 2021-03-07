<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\attraction\models\forms\PaxForm */
/* @var $form yii\bootstrap4\ActiveForm */

$pjaxId = 'pjax-attraction-pax-form';
?>

<div class="attraction-pax-form">

    <script>
        pjaxOffFormSubmit('#<?=$pjaxId?>');
    </script>
    <?php \yii\widgets\Pjax::begin(['id' => $pjaxId, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
        <?php
        $form = ActiveForm::begin([
            'options' => ['data-pjax' => true],
            'action' => ['/attraction/attraction-pax/create-ajax', 'id' => $model->atn_attraction_id],
            'method' => 'post'
        ]);
        ?>

        <?= $form->field($model, 'atn_attraction_id')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'product_id')->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'atn_pax_list')->widget(\unclead\multipleinput\MultipleInput::class, [
            'id' => 'hotel_room_create_multiple_input',
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
                    'name' => 'atnp_id',
                    'type' =>  \unclead\multipleinput\MultipleInputColumn::TYPE_HIDDEN_INPUT,
                    'value' => static function ($data) {
                        return $data['atnp_id'] ?? '';
                    },
                ],
                [

                    'name' => 'atnp_type_id',
                    'type' => \unclead\multipleinput\MultipleInputColumn::TYPE_DROPDOWN,
                    'title' => 'Pax type',
                    'value' => static function ($data) {
                        return $data['atnp_type_id'] ?? '';
                    },
                    'items' => \yii\helpers\ArrayHelper::merge(['' => '---'], \modules\attraction\models\AttractionPax::PAX_LIST),
                    'headerOptions' => [
                        'style' => 'width: atnp_type_id;',
                    ]
                ],
                [
                    'name' => 'atnp_age',
                    'type' =>  \unclead\multipleinput\MultipleInputColumn::TYPE_TEXT_INPUT,
                    'title' => 'Age',
                    'value' => static function ($data) {
                        return $data['atnp_age'] ?? '';
                    },
                    //'items' => \modules\hotel\models\HotelRoomPax::getPaxTypeList(),
                    'headerOptions' => [
                        //'style' => 'width: 80px;',
                    ],
                    'options' => [
                        'class' => 'form-control input_atnp_age',
                    ]
                ],

                [
                    'name'  => 'atnp_dob',
                    'type'  => \dosamigos\datepicker\DatePicker::class,
                    'title' => 'Date of birth',
                    'value' => static function ($data) {
                        return $data['atnp_dob'] ?? '';
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
                                        $(e.target).closest("tr").find(".input_atnp_age").val(age == 0 ? ++age : age);
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

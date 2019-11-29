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
            'action' => ['/hotel/hotel-room/update-ajax', 'id' => $model->hr_id],
            'method' => 'post'
        ]);
        ?>

        <?//= $form->field($model, 'hr_hotel_id')->textInput() ?>

        <?= $form->field($model, 'hr_room_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'phones')->widget(\unclead\multipleinput\MultipleInput::class, [
            'max' => 10,
            'enableError' => true,
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
                    'name' => 'type',
                    'type' => \unclead\multipleinput\MultipleInputColumn::TYPE_DROPDOWN,
                    'title' => 'Pax type',
                    'value' => static function ($model) {
                        return \modules\hotel\models\HotelRoomPax::PAX_TYPE_CHD;
                    },
                    'items' => \modules\hotel\models\HotelRoomPax::getPaxTypeList(),
                    'headerOptions' => [
                        'style' => 'width: 80px;',
                    ]
                ],
                [
                    'name' => 'age',
                    'type' => \unclead\multipleinput\MultipleInputColumn::TYPE_TEXT_INPUT,
                    'title' => 'Age',
                    'value' => static function ($model) {
                        return 1;
                    },
                    //'items' => \modules\hotel\models\HotelRoomPax::getPaxTypeList(),
                    'headerOptions' => [
                        'style' => 'width: 80px;',
                    ]
                ],

            ]
        ])->label(false) ?>

        <div class="form-group text-center">
            <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    <?php \yii\widgets\Pjax::end(); ?>

</div>

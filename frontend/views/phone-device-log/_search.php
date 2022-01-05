<?php

use dosamigos\datetimepicker\DateTimePicker;
use kartik\select2\Select2;
use sales\model\voip\phoneDevice\log\PhoneDeviceLogLevel;
use sales\widgets\UserSelect2Widget;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\voip\phoneDevice\log\PhoneDeviceLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="phone-device-log-search">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' => [
            'data-pjax' => true
        ],
    ]); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="well profile_view">

                    <div class="row">
                        <div class="col-md-2">
                            <?= $form->field($model, 'pdl_id') ?>
                        </div>
                        <div class="col-md-2">
                            <?= $form->field($model, 'pdl_user_id')->widget(UserSelect2Widget::class, [
                                'data' => $model->pdl_user_id ? [
                                    $model->pdl_user_id => $model->user->username
                                ] : [],
                            ]) ?>
                        </div>
                        <div class="col-md-2">
                            <?= $form->field($model, 'pdl_device_id') ?>
                        </div>
                        <div class="col-md-2">
                            <?= $form->field($model, 'pdl_level')->dropDownList(PhoneDeviceLogLevel::getList(), ['prompt' => 'Select level']) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'after_timestamp')->widget(DateTimePicker::class) ?>
                        </div>
                        <div class="col-md-12">
                            <?= $form->field($model, 'pdl_message') ?>
                        </div>
                        <div class="col-md-6">
                            <h2><i class="fa fa-list"></i> Show fields</h2>
                            <?= $form->field($model, 'show_fields')->widget(Select2::class, [
                                'data' => $model->getViewFields(),
                                'size' => Select2::SIZE_SMALL,
                                'pluginOptions' => [
                                    'closeOnSelect' => false,
                                    'allowClear' => true,
                                    //'width' => '100%',
                                ],
                                'options' => [
                                    'placeholder' => 'Choose additional fields...',
                                    'multiple' => true,
                                    //  'id' => 'showFields',
                                ],
                                //'value' => $model->show_fields,
                            ])->label(false) ?>
                        </div>
                    </div>
            </div>
        </div>
    </div>

    <div class="row">

    </div>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= \yii\helpers\Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset data', ['/phone-device-log/index'], ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

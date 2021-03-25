<?php

use kartik\select2\Select2;
use modules\order\src\entities\order\OrderStatus;
use modules\order\src\entities\order\search\OrderSearch;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model OrderSearch */
/* @var $form yii\widgets\ActiveForm */
/** @var \sales\access\ListsAccess $lists */

?>

<div class="order-search-block">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
        'id' => 'order_form_search',
    ]); ?>

    <div class="row">
        <div class="col-md-12 col-sm-12 profile_details">
            <div class="well profile_view">
                <div class="col-sm-12">
                    <h4 class="brief"><i>Order</i></h4>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'or_id')->textInput() ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'or_gid')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>
                </div>
                <div class=" profile-bottom text-center">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-sm-12 profile_details">
            <div class="well profile_view">
                <div class="col-sm-12">
                    <h4 class="brief"><i>Common</i></h4>
                    <div class="row">
                        <div class="col-md-3">
                            <?= $form->field($model, 'or_status_id')->dropDownList(OrderStatus::getList(), ['prompt' => '-']) ?>
                        </div>
                        <div class="col-md-3">
                            <?php echo $form->field($model, 'or_owner_user_id')->widget(Select2::class, [
                                'data' => $lists->getEmployees(true),
                                'size' => Select2::SMALL,
                                'options' => ['placeholder' => 'Select user', 'multiple' => false],
                                'pluginOptions' => ['allowClear' => true],
                            ]); ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'createdRangeTime', [
                                'options' => ['class' => 'form-group']
                            ])->widget(\kartik\daterange\DateRangePicker::class, [
                                'presetDropdown' => false,
                                'hideInput' => true,
                                'convertFormat' => true,
                                'pluginOptions' => [
                                    'timePicker' => true,
                                    'timePickerIncrement' => 1,
                                    'timePicker24Hour' => true,
                                    'locale' => [
                                        'format' => 'd-M-Y H:i',
                                        'separator' => ' - '
                                    ]
                                ]
                            ])->label('Created From / To') ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'updatedRangeTime', [
                                'options' => ['class' => 'form-group']
                            ])->widget(\kartik\daterange\DateRangePicker::class, [
                                'presetDropdown' => false,
                                'hideInput' => true,
                                'convertFormat' => true,
                                'pluginOptions' => [
                                    'timePicker' => true,
                                    'timePickerIncrement' => 1,
                                    'timePicker24Hour' => true,
                                    'locale' => [
                                        'format' => 'd-M-Y H:i',
                                        'separator' => ' - '
                                    ]
                                ]
                            ])->label('Updated From / To') ?>
                        </div>
                    </div>
                </div>
                <div class=" profile-bottom text-center">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h2><i class="fa fa-list"></i> Show Additional fields</h2>
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
                    'id' => 'showFields',
                ],
                //'value' => $model->show_fields,
            ])->label(false) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <br>
            <div class="form-group text-center">
                <?= Html::submitButton('<i class="fa fa-search"></i> Search leads', ['name' => 'search', 'class' => 'btn btn-primary search_orders_btn']) ?>
                <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset data', ['/order/order/search'], ['class' => 'btn btn-warning']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

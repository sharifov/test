<?php

use kartik\select2\Select2;
use modules\shiftSchedule\src\entities\shiftScheduleTypeLabel\ShiftScheduleTypeLabel;
use modules\shiftSchedule\src\forms\ShiftScheduleTypeForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model ShiftScheduleTypeForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="shift-schedule-type-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4">
        <?= $form->field($model, 'sst_key')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'sst_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'sst_title')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'sst_enabled')->checkbox() ?>

        <?php
            echo $form->field($model, 'sst_label_list', ['options' => ['class' => 'form-group']])
                ->widget(Select2::class, [
                'data' => ShiftScheduleTypeLabel::getList(),
                'size' => Select2::SMALL,
                'options' => ['placeholder' => 'Select type labels', 'multiple' => true],
                'pluginOptions' => ['allowClear' => true],
            ]);
            ?>




        <?php /*= $form->field($model, 'sst_readonly')->checkbox()*/ ?>

        <?php /*= $form->field($model, 'sst_work_time')->checkbox()*/ ?>






    </div>


        <div class="col-md-4">

            <?php

            try {
                echo $form->field($model, 'sst_params_json')->widget(
                    \kdn\yii2\JsonEditor::class,
                    [
                        'clientOptions' => [
                            'modes' => ['code', 'form', 'tree', 'view'], //'text',
                            'mode' => $model->isNewRecord ? 'code' : 'form'
                        ],
                        //'collapseAll' => ['view'],
                        'expandAll' => ['tree', 'form'],
                        'value' => $model->sst_params_json ? json_encode($model->sst_params_json) : ''
                    ]
                );
            } catch (Exception $exception) {
                echo $form->field($model, 'sst_params_json')
                    ->textarea(['rows' => 6, 'value' => json_encode($model->sst_params_json)]);
            }

            ?>

        <div class="col-md-4">
            <?= $form->field($model, 'sst_color')->input('color') ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'sst_icon_class')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'sst_css_class')->textInput(['maxlength' => true]) ?>
        </div>

            <div class="col-md-4">
                <?= $form->field($model, 'sst_sort_order')->input('number', ['min' => 0]) ?>
            </div>


    </div>
    </div>

    <div class="row">
        <div class="col-md-12 text-left">
            <div class="form-group">
                <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\shiftSchedule\src\entities\shiftScheduleTypeLabelAssign\ShiftScheduleTypeLabelAssign */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="shift-schedule-type-label-assign-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="col-md-3">
        <?php
            echo $form->field($model, 'tla_stl_key')->widget(\kartik\select2\Select2::class, [
                'data' => \modules\shiftSchedule\src\entities\shiftScheduleTypeLabel\ShiftScheduleTypeLabel::getList(),
                'size' => \kartik\select2\Select2::SMALL,
                'options' => ['placeholder' => 'Select Label', 'multiple' => false],
                'pluginOptions' => ['allowClear' => true],
            ]);
            ?>

        <?php
            echo $form->field($model, 'tla_sst_id')->widget(\kartik\select2\Select2::class, [
                'data' => \modules\shiftSchedule\src\entities\shiftScheduleType\ShiftScheduleType::getList(),
                'size' => \kartik\select2\Select2::SMALL,
                'options' => ['placeholder' => 'Select Shift Type', 'multiple' => false],
                'pluginOptions' => ['allowClear' => true],
            ]);
            ?>
        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>
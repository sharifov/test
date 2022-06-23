<?php

use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\objectSegment\src\entities\ObjectSegmentTask */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="object-segment-task-form">

    <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'ostl_osl_id', [])->widget(Select2::class, [
                    'data' => $model->getObjectList(),
                    'size' => Select2::SMALL,
                    'options' => ['placeholder' => 'Select List Object'],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>

                <?= $form->field($model, 'ostl_tl_id', [])->widget(Select2::class, [
                    'data' => $model->getTaskListAsKeyValue(),
                    'size' => Select2::SMALL,
                    'options' => ['placeholder' => 'Select Task Object'],
                    'pluginOptions' => ['allowClear' => true],
                ]) ?>

                <div class="form-group">
                    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                </div>
            </div>
        </div>

    <?php ActiveForm::end(); ?>

</div>

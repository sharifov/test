<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\shiftSchedule\entity\shift\Shift */
/* @var $form ActiveForm */
?>

<div class="shift-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'sh_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'sh_enabled')->checkbox() ?>

        <?= $form->field($model, 'sh_color')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'sh_sort_order')->input('number') ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

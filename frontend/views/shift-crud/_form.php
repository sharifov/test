<?php

use modules\shiftSchedule\src\entities\shiftCategory\ShiftCategoryQuery;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \modules\shiftSchedule\src\entities\shift\Shift */
/* @var $form ActiveForm */
?>

<div class="shift-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'sh_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'sh_title')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'sh_category_id')->dropDownList(ShiftCategoryQuery::getList(), [
            'prompt' => '--'
        ]) ?>

        <?= $form->field($model, 'sh_enabled')->checkbox() ?>

        <div class="col-md-6">
            <?= $form->field($model, 'sh_color')->input('color') ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'sh_sort_order')->input('number') ?>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

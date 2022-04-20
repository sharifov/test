<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \modules\shiftSchedule\src\entities\shiftCategory\search\ShiftCategorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="shift-category-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'sc_id') ?>

    <?= $form->field($model, 'sc_name') ?>

    <?= $form->field($model, 'sc_created_user_id') ?>

    <?= $form->field($model, 'sc_updated_user_id') ?>

    <?= $form->field($model, 'sc_created_dt') ?>

    <?php // echo $form->field($model, 'sc_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

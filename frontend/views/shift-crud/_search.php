<?php

use common\components\bootstrap4\activeForm\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model \modules\shiftSchedule\src\entities\shift\search\SearchShift */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="shift-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'sh_id') ?>

    <?= $form->field($model, 'sh_name') ?>

    <?= $form->field($model, 'sh_enabled') ?>

    <?= $form->field($model, 'sh_color') ?>

    <?= $form->field($model, 'sh_sort_order') ?>

    <?php // echo $form->field($model, 'sh_created_dt') ?>

    <?php // echo $form->field($model, 'sh_updated_dt') ?>

    <?php // echo $form->field($model, 'sh_created_user_id') ?>

    <?php // echo $form->field($model, 'sh_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

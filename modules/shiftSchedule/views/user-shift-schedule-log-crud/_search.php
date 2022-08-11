<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\shiftSchedule\src\entities\userShiftScheduleLog\search\UserShiftScheduleLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-shift-schedule-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ussl_id') ?>

    <?= $form->field($model, 'ussl_uss_id') ?>

    <?= $form->field($model, 'ussl_old_attr') ?>

    <?= $form->field($model, 'ussl_new_attr') ?>

    <?= $form->field($model, 'ussl_formatted_attr') ?>

    <?php // echo $form->field($model, 'ussl_created_user_id') ?>

    <?php // echo $form->field($model, 'ussl_created_dt') ?>

    <?php // echo $form->field($model, 'ussl_month_start') ?>

    <?php // echo $form->field($model, 'ussl_year_start') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\shiftSchedule\src\entities\shiftScheduleRequestLog\search\ShiftScheduleRequestLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="shift-schedule-request-history-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ssrh_id') ?>

    <?= $form->field($model, 'ssrh_ssr_id') ?>

    <?= $form->field($model, 'ssrh_old_attr') ?>

    <?= $form->field($model, 'ssrh_new_attr') ?>

    <?= $form->field($model, 'ssrh_formatted_attr') ?>

    <?php // echo $form->field($model, 'ssrh_created_dt') ?>

    <?php // echo $form->field($model, 'ssrh_updated_dt') ?>

    <?php // echo $form->field($model, 'ssrh_created_user_id') ?>

    <?php // echo $form->field($model, 'ssrh_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

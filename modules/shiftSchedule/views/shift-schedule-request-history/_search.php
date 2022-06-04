<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\shiftSchedule\src\entities\shiftScheduleRequestHistory\search\ShiftScheduleRequestHistorySearch */
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

    <?= $form->field($model, 'ssrh_from_status_id') ?>

    <?= $form->field($model, 'ssrh_to_status_id') ?>

    <?= $form->field($model, 'ssrh_from_description') ?>

    <?php // echo $form->field($model, 'ssrh_to_description') ?>

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

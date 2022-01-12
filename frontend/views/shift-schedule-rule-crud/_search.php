<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\shiftSchedule\entity\shiftScheduleRule\search\SearchShiftScheduleRule */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="shift-schedule-rule-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ssr_id') ?>

    <?= $form->field($model, 'ssr_shift_id') ?>

    <?= $form->field($model, 'ssr_title') ?>

    <?= $form->field($model, 'ssr_timezone') ?>

    <?= $form->field($model, 'ssr_start_time_loc') ?>

    <?php // echo $form->field($model, 'ssr_end_time_loc') ?>

    <?php // echo $form->field($model, 'ssr_duration_time') ?>

    <?php // echo $form->field($model, 'ssr_cron_expression') ?>

    <?php // echo $form->field($model, 'ssr_cron_expression_exclude') ?>

    <?php // echo $form->field($model, 'ssr_enabled') ?>

    <?php // echo $form->field($model, 'ssr_start_time_utc') ?>

    <?php // echo $form->field($model, 'ssr_end_time_utc') ?>

    <?php // echo $form->field($model, 'ssr_created_dt') ?>

    <?php // echo $form->field($model, 'ssr_updated_dt') ?>

    <?php // echo $form->field($model, 'ssr_created_user_id') ?>

    <?php // echo $form->field($model, 'ssr_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

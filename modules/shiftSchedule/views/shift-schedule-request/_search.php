<?php

/**
 * @var View $this
 * @var ShiftScheduleRequestSearch $model
 * @var ActiveForm $form
 */

use modules\shiftSchedule\src\entities\shiftScheduleRequest\search\ShiftScheduleRequestSearch;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

?>

<div class="shift-schedule-request-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'ssr_id') ?>

    <?= $form->field($model, 'ssr_uss_id') ?>

    <?= $form->field($model, 'ssr_sst_id') ?>

    <?= $form->field($model, 'ssr_status_id') ?>

    <?= $form->field($model, 'ssr_description') ?>

    <?php // echo $form->field($model, 'ssr_created_dt') ?>

    <?php // echo $form->field($model, 'ssr_update_dt') ?>

    <?php // echo $form->field($model, 'ssr_created_user_id') ?>

    <?php // echo $form->field($model, 'ssr_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

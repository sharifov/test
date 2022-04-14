<?php

use common\components\bootstrap4\activeForm\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model \modules\shiftSchedule\src\entities\userShiftSchedule\search\SearchUserShiftSchedule */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="user-shift-schedule-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'uss_id') ?>

    <?= $form->field($model, 'uss_user_id') ?>

    <?= $form->field($model, 'uss_shift_id') ?>

    <?= $form->field($model, 'uss_ssr_id') ?>

    <?= $form->field($model, 'uss_description') ?>

    <?php // echo $form->field($model, 'uss_start_utc_dt') ?>

    <?php // echo $form->field($model, 'uss_end_utc_dt') ?>

    <?php // echo $form->field($model, 'uss_duration') ?>

    <?php // echo $form->field($model, 'uss_status_id') ?>

    <?php // echo $form->field($model, 'uss_type_id') ?>

    <?php // echo $form->field($model, 'uss_customized') ?>

    <?php // echo $form->field($model, 'uss_created_dt') ?>

    <?php // echo $form->field($model, 'uss_updated_dt') ?>

    <?php // echo $form->field($model, 'uss_created_user_id') ?>

    <?php // echo $form->field($model, 'uss_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

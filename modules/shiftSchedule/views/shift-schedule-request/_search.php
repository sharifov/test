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

    <?= $form->field($model, 'srh_id') ?>

    <?= $form->field($model, 'srh_uss_id') ?>

    <?= $form->field($model, 'srh_sst_id') ?>

    <?= $form->field($model, 'srh_status_id') ?>

    <?= $form->field($model, 'srh_description') ?>

    <?php // echo $form->field($model, 'srh_created_dt') ?>
srh_update_user_id
    <?php // echo $form->field($model, 'srh_update_dt') ?>

    <?php // echo $form->field($model, 'srh_created_user_id') ?>

    <?php // echo $form->field($model, 'srh_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

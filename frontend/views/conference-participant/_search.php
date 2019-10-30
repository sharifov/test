<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\ConferenceParticipantSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="conference-participant-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cp_id') ?>

    <?= $form->field($model, 'cp_cf_id') ?>

    <?= $form->field($model, 'cp_call_sid') ?>

    <?= $form->field($model, 'cp_call_id') ?>

    <?= $form->field($model, 'cp_status_id') ?>

    <?php // echo $form->field($model, 'cp_join_dt') ?>

    <?php // echo $form->field($model, 'cp_leave_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

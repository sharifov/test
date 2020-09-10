<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\conference\entity\conferenceParticipantStats\search\ConferenceParticipantStatsSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="conference-participant-stats-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cps_id') ?>

    <?= $form->field($model, 'cps_cf_id') ?>

    <?= $form->field($model, 'cps_cf_sid') ?>

    <?= $form->field($model, 'cps_participant_identity') ?>

    <?= $form->field($model, 'cps_user_id') ?>

    <?php // echo $form->field($model, 'cps_created_dt') ?>

    <?php // echo $form->field($model, 'cps_duration') ?>

    <?php // echo $form->field($model, 'cps_talk_time') ?>

    <?php // echo $form->field($model, 'cps_hold_time') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

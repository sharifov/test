<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\conference\entity\conferenceRecordingLog\search\ConferenceRecordingLogSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="conference-recording-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cfrl_id') ?>

    <?= $form->field($model, 'cfrl_conference_sid') ?>

    <?= $form->field($model, 'cfrl_user_id') ?>

    <?= $form->field($model, 'cfrl_created_dt') ?>

    <?= $form->field($model, 'cfrl_year') ?>

    <?php // echo $form->field($model, 'cfrl_month') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

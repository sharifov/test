<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\conference\entity\conferenceEventLog\search\ConferenceEventLogSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="conference-event-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cel_id') ?>

    <?= $form->field($model, 'cel_event_type') ?>

    <?= $form->field($model, 'cel_conference_sid') ?>

    <?= $form->field($model, 'cel_sequence_number') ?>

    <?= $form->field($model, 'cel_created_dt') ?>

    <?php // echo $form->field($model, 'cel_data') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

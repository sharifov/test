<?php

use common\models\Conference;
use sales\widgets\DateTimePicker;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\conference\entity\conferenceEventLog\ConferenceEventLog */
/* @var $form ActiveForm */
?>

<div class="conference-event-log-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'cel_event_type')->dropDownList(Conference::EVENT_LIST, ['prompt' => 'Select event']) ?>

        <?= $form->field($model, 'cel_conference_sid')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cel_sequence_number')->textInput() ?>

        <?= $form->field($model, 'cel_created_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'cel_data')->textarea(['rows' => 6]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\ConferenceRoomSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="conference-room-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cr_id') ?>

    <?= $form->field($model, 'cr_key') ?>

    <?= $form->field($model, 'cr_name') ?>

    <?= $form->field($model, 'cr_phone_number') ?>

    <?= $form->field($model, 'cr_enabled') ?>

    <?php // echo $form->field($model, 'cr_start_dt') ?>

    <?php // echo $form->field($model, 'cr_end_dt') ?>

    <?php // echo $form->field($model, 'cr_param_muted') ?>

    <?php // echo $form->field($model, 'cr_param_beep') ?>

    <?php // echo $form->field($model, 'cr_param_start_conference_on_enter') ?>

    <?php // echo $form->field($model, 'cr_param_end_conference_on_enter') ?>

    <?php // echo $form->field($model, 'cr_param_max_participants') ?>

    <?php // echo $form->field($model, 'cr_param_record') ?>

    <?php // echo $form->field($model, 'cr_param_region') ?>

    <?php // echo $form->field($model, 'cr_param_trim') ?>

    <?php // echo $form->field($model, 'cr_param_wait_url') ?>

    <?php // echo $form->field($model, 'cr_moderator_phone_number') ?>

    <?php // echo $form->field($model, 'cr_welcome_message') ?>

    <?php // echo $form->field($model, 'cr_created_dt') ?>

    <?php // echo $form->field($model, 'cr_updated_dt') ?>

    <?php // echo $form->field($model, 'cr_created_user_id') ?>

    <?php // echo $form->field($model, 'cr_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

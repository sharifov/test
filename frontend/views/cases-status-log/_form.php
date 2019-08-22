<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\entities\cases\CasesStatusLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cases-status-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'csl_case_id')->textInput() ?>

    <?= $form->field($model, 'csl_from_status')->textInput() ?>

    <?= $form->field($model, 'csl_to_status')->textInput() ?>

    <?= $form->field($model, 'csl_start_dt')->textInput() ?>

    <?= $form->field($model, 'csl_end_dt')->textInput() ?>

    <?= $form->field($model, 'csl_time_duration')->textInput() ?>

    <?= $form->field($model, 'csl_created_user_id')->textInput() ?>

    <?= $form->field($model, 'csl_owner_id')->textInput() ?>

    <?= $form->field($model, 'csl_description')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LeadCallExpert */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-call-expert-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-4">
        <?= $form->field($model, 'lce_lead_id')->textInput() ?>

        <?= $form->field($model, 'lce_request_text')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'lce_request_dt')->textInput() ?>

        <?= $form->field($model, 'lce_status_id')->dropDownList(\common\models\LeadCallExpert::STATUS_LIST, ['prompt' => '---']) ?>

        <?= $form->field($model, 'lce_agent_user_id')->textInput() ?>

    </div>

    <div class="col-md-4">

        <?= $form->field($model, 'lce_response_text')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'lce_response_lead_quotes')->textarea(['rows' => 3]) ?>

        <?= $form->field($model, 'lce_response_dt')->textInput() ?>


        <?= $form->field($model, 'lce_expert_user_id')->textInput() ?>

        <?= $form->field($model, 'lce_expert_username')->textInput(['maxlength' => true]) ?>
    </div>

    <?php //= $form->field($model, 'lce_updated_dt')->textInput() ?>
    <div class="col-md-12">
        <div class="form-group">
            <?= Html::submitButton('Save Call Expert', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

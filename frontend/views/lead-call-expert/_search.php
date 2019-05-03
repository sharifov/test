<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\LeadCallExpertSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-call-expert-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'lce_id') ?>

    <?= $form->field($model, 'lce_lead_id') ?>

    <?= $form->field($model, 'lce_request_text') ?>

    <?= $form->field($model, 'lce_request_dt') ?>

    <?= $form->field($model, 'lce_response_text') ?>

    <?php // echo $form->field($model, 'lce_response_lead_quotes') ?>

    <?php // echo $form->field($model, 'lce_response_dt') ?>

    <?php // echo $form->field($model, 'lce_status_id') ?>

    <?php // echo $form->field($model, 'lce_agent_user_id') ?>

    <?php // echo $form->field($model, 'lce_expert_user_id') ?>

    <?php // echo $form->field($model, 'lce_expert_username') ?>

    <?php // echo $form->field($model, 'lce_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

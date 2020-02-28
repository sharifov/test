<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\VisitorLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="visitor-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'vl_project_id')->textInput() ?>

    <?= $form->field($model, 'vl_source_cid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_ga_client_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_ga_user_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_user_id')->textInput() ?>

    <?= $form->field($model, 'vl_client_id')->textInput() ?>

    <?= $form->field($model, 'vl_lead_id')->textInput() ?>

    <?= $form->field($model, 'vl_gclid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_dclid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_utm_source')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_utm_medium')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_utm_campaign')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_utm_term')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_utm_content')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_referral_url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_location_url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_user_agent')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_ip_address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vl_visit_dt')->textInput() ?>

    <?= $form->field($model, 'vl_created_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

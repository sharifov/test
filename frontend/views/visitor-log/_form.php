<?php

use sales\access\ListsAccess;
use sales\auth\Auth;
use sales\widgets\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\VisitorLog */
/* @var $form yii\widgets\ActiveForm */

$list = (new ListsAccess(Auth::id()));
?>

<div class="visitor-log-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'vl_project_id')->dropDownList($list->getProjects(), ['prompt' => 'Select project']) ?>

        <?= $form->field($model, 'vl_source_cid')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'vl_ga_client_id')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'vl_ga_user_id')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'vl_customer_id')->textInput() ?>

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

        <?= $form->field($model, 'vl_visit_dt')->widget(DateTimePicker::class) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
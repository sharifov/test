<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\VisitorLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="visitor-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'vl_id') ?>

    <?= $form->field($model, 'vl_project_id') ?>

    <?= $form->field($model, 'vl_source_cid') ?>

    <?= $form->field($model, 'vl_ga_client_id') ?>

    <?= $form->field($model, 'vl_ga_user_id') ?>

    <?php // echo $form->field($model, 'vl_user_id') ?>

    <?php // echo $form->field($model, 'vl_client_id') ?>

    <?php // echo $form->field($model, 'vl_lead_id') ?>

    <?php // echo $form->field($model, 'vl_gclid') ?>

    <?php // echo $form->field($model, 'vl_dclid') ?>

    <?php // echo $form->field($model, 'vl_utm_source') ?>

    <?php // echo $form->field($model, 'vl_utm_medium') ?>

    <?php // echo $form->field($model, 'vl_utm_campaign') ?>

    <?php // echo $form->field($model, 'vl_utm_term') ?>

    <?php // echo $form->field($model, 'vl_utm_content') ?>

    <?php // echo $form->field($model, 'vl_referral_url') ?>

    <?php // echo $form->field($model, 'vl_location_url') ?>

    <?php // echo $form->field($model, 'vl_user_agent') ?>

    <?php // echo $form->field($model, 'vl_ip_address') ?>

    <?php // echo $form->field($model, 'vl_visit_dt') ?>

    <?php // echo $form->field($model, 'vl_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

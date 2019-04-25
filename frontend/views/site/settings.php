<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Site Settings';
?>

<div class="col-md-4">

    <h1><?=$this->title?></h1>
    <?php $form = ActiveForm::begin(['id' => 'site-settings-form']); ?>

    <?= $form->field($model, 'siteName') ?>
    <?= $form->field($model, 'siteDescription') ?>

    <?= $form->field($model, 'enable_lead_inbox')->dropDownList(['0' => 0, '1' => 1], ['prompt' => '---']) ?>

    <?= Html::submitButton('<span class="fa fa-save"></span> Save Settings', ['class' => 'btn btn-success']) ?>

    <?php ActiveForm::end(); ?>

</div>
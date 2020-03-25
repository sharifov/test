<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\callLog\entity\callLogLead\CallLogLead */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="call-log-lead-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cll_cl_id')->textInput() ?>

    <?= $form->field($model, 'cll_lead_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

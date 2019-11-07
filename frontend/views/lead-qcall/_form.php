<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LeadQcall */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-qcall-form">

    <?php $form = ActiveForm::begin(); ?>
<div class="col-md-4">
    <?= $form->field($model, 'lqc_lead_id')->input('number', ['min' => 1]) ?>

    <?= $form->field($model, 'lqc_dt_from')->textInput() ?>

    <?= $form->field($model, 'lqc_dt_to')->textInput() ?>

    <?= $form->field($model, 'lqc_weight')->input('number', ['min' => 0]) ?>

    <?= $form->field($model, 'lqc_created_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
</div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\QcallConfig */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="qcall-config-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-4">
    <?= $form->field($model, 'qc_status_id')->dropDownList(\common\models\Lead::getStatusList(), ['prompt' => '-']) ?>

    <?= $form->field($model, 'qc_call_att')->input('number', ['min' => 0]) ?>

    <?= $form->field($model, 'qc_client_time_enable')->checkbox() ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'qc_time_from')->input('number', ['min' => 0]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'qc_time_to')->input('number', ['min' => 0]) ?>
            </div>
        </div>

<!--    --><?//= $form->field($model, 'qc_created_dt')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'qc_updated_dt')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'qc_created_user_id')->textInput() ?>
<!---->
<!--    --><?//= $form->field($model, 'qc_updated_user_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save Config', ['class' => 'btn btn-success']) ?>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

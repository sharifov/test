<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\callLogFilterGuard\entity\CallLogFilterGuard */
/* @var $form ActiveForm */
?>

<div class="call-log-filter-guard-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'clfg_type')->textInput() ?>

        <?= $form->field($model, 'clfg_sd_rate')->textInput() ?>

        <?= $form->field($model, 'clfg_trust_percent')->textInput() ?>

        <?= $form->field($model, 'clfg_cpl_id')->textInput() ?>

        <?= $form->field($model, 'clfg_call_log_id')->textInput() ?>

        <?= $form->field($model, 'clfg_redial_status')->dropDownList(\common\models\Call::STATUS_LIST, ['prompt' => '--']) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\callLogFilterGuard\entity\CallLogFilterGuard */
/* @var $form ActiveForm */
?>

<div class="call-log-filter-guard-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'clfg_type')->textInput() ?>

        <?= $form->field($model, 'clfg_sd_rate')->textInput() ?>

        <?= $form->field($model, 'clfg_trust_percent')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\requestControl\models\RequestControlRule */
/* @var $form ActiveForm */

?>

<div class="request-control-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'rcr_type')->dropDownList($this->context->module->ruleTypeList()) ?>

        <?= $form->field($model, 'rcr_subject')->textInput() ?>

        <?= $form->field($model, 'rcr_local')->textInput() ?>

        <?= $form->field($model, 'rcr_global')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

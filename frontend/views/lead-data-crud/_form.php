<?php

use src\model\leadDataKey\entity\LeadDataKey;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\leadData\entity\LeadData */
/* @var $form ActiveForm */
?>

<div class="lead-data-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ld_lead_id')->textInput() ?>

        <?= $form->field($model, 'ld_field_key')->dropDownList(LeadDataKey::getListCache(), ['prompt' => '-']) ?>

        <?= $form->field($model, 'ld_field_value')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

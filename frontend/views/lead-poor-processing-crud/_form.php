<?php

use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataQuery;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;
use src\widgets\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model src\model\leadPoorProcessing\entity\LeadPoorProcessing */
/* @var $form ActiveForm */
?>

<div class="lead-poor-processing-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'lpp_lead_id')->textInput() ?>

        <?= $form->field($model, 'lpp_lppd_id')->dropDownList(LeadPoorProcessingDataQuery::getList()) ?>

        <?= $form->field($model, 'lpp_expiration_dt')->widget(DateTimePicker::class) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

<?php

use src\model\leadUserConversion\service\LeadUserConversionDictionary;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\leadUserConversion\entity\LeadUserConversion */
/* @var $form ActiveForm */
?>

<div class="lead-user-conversion-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'luc_lead_id')->textInput() ?>

        <?= $form->field($model, 'luc_user_id')->dropDownList(\common\models\Employee::getList(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'luc_description')->dropDownList(LeadUserConversionDictionary::DESCRIPTION_LIST) ?>

        <?= $form->field($model, 'luc_created_user_id')->dropDownList(\common\models\Employee::getList(), ['prompt' => '---']) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

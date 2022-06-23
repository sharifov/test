<?php

use common\models\Employee;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ProfitSplit */
/* @var $form yii\widgets\ActiveForm */

?>
<div class="profit-split-form row">
    <div class="col-md-6">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ps_lead_id')->textInput() ?>

        <?php echo $form->field($model, 'ps_user_id')->widget(Select2::class, [
            'data' => Employee::getList(),
            'size' => Select2::SMALL,
            'options' => ['placeholder' => 'Select User'],
            'pluginOptions' => ['allowClear' => true],
        ]); ?>

        <?= $form->field($model, 'ps_percent')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

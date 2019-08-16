<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model \sales\forms\cases\CasesChangeStatusForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cases-change-status">

    <?php $form = ActiveForm::begin([
        'action' => ['cases/change-status'],
        'enableAjaxValidation' => true,
        'validationUrl' => \yii\helpers\Url::to(['cases/change-status-validate']),
        'method' => 'post',
    ]); ?>

    <?= $form->field($model, 'case_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'status')->dropDownList($model->getStatusList(), ['prompt' => '-']) ?>

    <?= $form->field($model, 'message')->textarea(['rows' => 3]) ?>

    <div class="form-group text-center">
        <?= Html::submitButton('Change Status', ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

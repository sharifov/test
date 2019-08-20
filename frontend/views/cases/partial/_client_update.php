<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model sales\forms\cases\CasesClientUpdateForm */
/* @var $form yii\widgets\ActiveForm */
?>
<?php Pjax::begin(['id' => 'pjax-cases-client-update-form', 'enablePushState' => false, 'enableReplaceState' => false]); ?>
<div class="cases-change-status">

    <?php $form = ActiveForm::begin([
        'action' => ['cases/client-update', 'gid' => $model->caseGid],
        'method' => 'post',
        'options' => ['data-pjax' => true]
    ]); ?>

    <?php
        echo $form->errorSummary($model);
    ?>

    <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'middle_name')->textInput(['maxlength' => true]) ?>

    <div class="form-group text-center">
        <?= Html::submitButton('Update', ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<?php Pjax::end(); ?>

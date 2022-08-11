<?php

use common\models\ClientEmail;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model src\forms\cases\CasesAddEmailForm */
/* @var $form yii\widgets\ActiveForm */
?>
<?php Pjax::begin(['id' => 'pjax-cases-add-email-form', 'enablePushState' => false, 'enableReplaceState' => false]); ?>
<div class="cases-change-status">

    <?php $form = ActiveForm::begin([
        'action' => ['cases/add-email', 'gid' => $model->caseGid],
        'method' => 'post',
        'options' => ['data-pjax' => true]
    ]); ?>

    <?php
        echo $form->errorSummary($model);
    ?>

    <?=
    $form->field($model, 'email', [
        'template' => '<div class="input-group"><span class="input-group-addon">@</span>{input}</div>{error}',
    ])->input('email', ['maxlength' => true, 'required' => true])
    ?>

    <?= $form->field($model, 'type')->dropDownList(ClientEmail::getEmailTypeList()) ?>

    <div class="form-group text-center">
        <?= Html::submitButton('Add Email', ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<?php Pjax::end(); ?>

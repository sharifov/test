<?php

use sales\model\cases\useCases\cases\updateInfo\UpdateInfoForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model UpdateInfoForm */
/* @var $form yii\widgets\ActiveForm */
/* @var $categoryList [] */
?>
<?php Pjax::begin(['id' => 'pjax-cases-update-form', 'enablePushState' => false, 'enableReplaceState' => false]); ?>
<div class="cases-change-status">

    <?php $form = ActiveForm::begin([
        'action' => ['/cases/ajax-update', 'gid' => $model->getCaseGid()],
        'method' => 'post',
        'options' => ['data-pjax' => true]
    ]); ?>

    <?php
        echo $form->errorSummary($model);
    ?>

    <?= $form->field($model, 'category')->dropDownList($model->getCategoryList(), ['prompt' => '-']) ?>

    <?= $form->field($model, 'orderUid')->textInput(['maxlength' => 7]) ?>

    <?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 4]) ?>

    <div class="form-group text-center">
        <?= Html::submitButton('Update', ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<?php Pjax::end(); ?>

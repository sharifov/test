<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model sales\forms\cases\CasesUpdateForm */
/* @var $form yii\widgets\ActiveForm */
/* @var $categoryList [] */
?>
<?php Pjax::begin(['id' => 'pjax-cases-update-form', 'enablePushState' => false, 'enableReplaceState' => false]); ?>
<div class="cases-change-status">

    <?php $form = ActiveForm::begin([
        'action' => ['cases/ajax-update', 'gid' => $model->caseGid],
        'method' => 'post',
        'options' => ['data-pjax' => true]
    ]); ?>

    <?php
        echo $form->errorSummary($model);
    ?>

    <?= $form->field($model, 'category')->dropDownList($categoryList, ['prompt' => '-']) ?>

    <div class="form-group text-center">
        <?= Html::submitButton('Update', ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<?php Pjax::end(); ?>

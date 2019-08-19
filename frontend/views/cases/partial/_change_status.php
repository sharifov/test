<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model sales\forms\cases\CasesChangeStatusForm */
/* @var $form yii\widgets\ActiveForm */
?>
<?php Pjax::begin(['id' => 'pjax-cases-change-status-form', 'enablePushState' => false, 'enableReplaceState' => false]); ?>
<div class="cases-change-status">

    <?php $form = ActiveForm::begin([
        'action' => ['cases/change-status', 'gid' => $model->caseGid],
        'method' => 'post',
        'options' => ['data-pjax' => true]
    ]); ?>

    <?php
    echo $form->errorSummary($model);
    ?>

    <?= $form->field($model, 'status')->dropDownList($model->getStatusList(), ['prompt' => '-']) ?>

    <?= $form->field($model, 'message')->textarea(['rows' => 3]) ?>

    <div class="form-group text-center">
        <?= Html::submitButton('Change Status', ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<?php Pjax::end(); ?>

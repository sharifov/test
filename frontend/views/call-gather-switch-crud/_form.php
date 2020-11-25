<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\call\entity\callCommand\CallGatherSwitch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="call-gather-switch-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-2">
    <?php //= $form->field($model, 'cgs_ccom_id')->input('number', ['step' => 1, 'min' => 1]) ?>

        <?= $form->field($model, 'cgs_ccom_id')->widget(\kartik\select2\Select2::class, [
            'data' => \sales\model\call\entity\callCommand\CallCommand::getList(true, \sales\model\call\entity\callCommand\CallCommand::TYPE_GATHER),
            'size' => \kartik\select2\Select2::SMALL,
            'options' => ['placeholder' => 'Select Command', 'multiple' => false],
            'pluginOptions' => ['allowClear' => true],
        ])
?>

    <?= $form->field($model, 'cgs_step')->dropDownList(array_combine(range(1, 10), range(1, 10)), ['prompt' => '-']) ?>

    <?= $form->field($model, 'cgs_case')->dropDownList(array_combine(range(0, 9), range(0, 9)), ['prompt' => '-']) ?>

    <?php //= $form->field($model, 'cgs_exec_ccom_id')->input('number', ['step' => 1, 'min' => 1]) ?>

        <?= $form->field($model, 'cgs_exec_ccom_id')->widget(\kartik\select2\Select2::class, [
            'data' => \sales\model\call\entity\callCommand\CallCommand::getList(true),
            'size' => \kartik\select2\Select2::SMALL,
            'options' => ['placeholder' => 'Select Command', 'multiple' => false],
            'pluginOptions' => ['allowClear' => true],
        ])
?>

    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success']) ?>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

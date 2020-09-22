<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\call\entity\callCommand\PhoneLineCommand */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="phone-line-command-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-lg-2">
    <?php //= $form->field($model, 'plc_line_id')->dropDownList(\sales\model\phoneLine\phoneLine\entity\PhoneLine::getList(), ['prompt' => '-']) ?>

        <?= $form->field($model, 'plc_line_id')->widget(\kartik\select2\Select2::class, [
            'data' => \sales\model\phoneLine\phoneLine\entity\PhoneLine::getList(),
            'size' => \kartik\select2\Select2::SMALL,
            'options' => ['placeholder' => 'Select Line', 'multiple' => false],
            'pluginOptions' => ['allowClear' => true],
        ])
        ?>


        <?= $form->field($model, 'plc_ccom_id')->widget(\kartik\select2\Select2::class, [
            'data' => \sales\model\call\entity\callCommand\CallCommand::getList(true),
            'size' => \kartik\select2\Select2::SMALL,
            'options' => ['placeholder' => 'Select Command', 'multiple' => false],
            'pluginOptions' => ['allowClear' => true],
        ])
        ?>

    <?php //= $form->field($model, 'plc_ccom_id')->textInput() ?>

    <?= $form->field($model, 'plc_sort_order')->input('number', ['step' => 1, 'min' => 0]) ?>

    <?php //= $form->field($model, 'plc_created_user_id')->textInput() ?>

    <?php //= $form->field($model, 'plc_created_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-save"></i>  Save', ['class' => 'btn btn-success']) ?>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

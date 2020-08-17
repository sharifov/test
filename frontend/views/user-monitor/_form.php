<?php

use sales\model\user\entity\monitor\UserMonitor;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\user\entity\monitor\UserMonitor */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-monitor-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="col-md-4">

        <?= $form->field($model, 'um_user_id')->textInput() ?>

        <?= $form->field($model, 'um_type_id')->dropDownList(UserMonitor::getTypeList(), ['prompt' => '-']) ?>

        <?= $form->field($model, 'um_start_dt')->textInput() ?>

        <?= $form->field($model, 'um_end_dt')->textInput() ?>

        <?= $form->field($model, 'um_period_sec')->input('number', ['min' => 0]) ?>

        <?= $form->field($model, 'um_description')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

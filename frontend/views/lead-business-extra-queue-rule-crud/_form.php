<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\leadBusinessExtraQueueRule\entity\LeadBusinessExtraQueueRule */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-business-extra-queue-rule-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'lbeqr_key')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lbeqr_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lbeqr_description')->textarea(['rows' => 6]) ?>

    <?php
    try {
        $model->lppd_params_json = \frontend\helpers\JsonHelper::encode($model->lppd_params_json);
        echo $form->field($model, 'lbeqr_params_json')->widget(
            \kdn\yii2\JsonEditor::class,
            [
                'clientOptions' => [
                    'modes' => ['code', 'form', 'tree', 'view'],
                    'mode' => 'code'
                ],
                'expandAll' => ['tree', 'form'],
            ]
        );
    } catch (Exception $exception) {
        $model->lbeqr_params_json = '{}';
        echo $form->field($model, 'lbeqr_params_json')->textarea(['rows' => 6]);
    }
    ?>

    <?= $form->field($model, 'lbeqr_duration')->textInput() ?>

    <?= $form->field($model, 'lbeqr_start_time')->textInput() ?>

    <?= $form->field($model, 'lbeqr_end_time')->textInput() ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

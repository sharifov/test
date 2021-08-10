<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use sales\helpers\app\AppHelper;
use frontend\helpers\JsonHelper;

/* @var $this yii\web\View */
/* @var $model sales\entities\cases\CaseEventLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="case-event-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cel_case_id')->textInput() ?>

    <?= $form->field($model, 'cel_description')->textInput(['maxlength' => true]) ?>

    <?php
    $model->cel_data_json = JsonHelper::encode($model->cel_data_json);
    try {
        echo $form->field($model, 'cel_data_json')->widget(
            \kdn\yii2\JsonEditor::class,
            [
                'clientOptions' => [
                    'modes' => ['code', 'form'],
                    'mode' => $model->isNewRecord ? 'code' : 'form'
                ],
                'expandAll' => ['tree', 'form'],
            ]
        );
    } catch (Exception $exception) {
        try {
            echo $form->field($model, 'cel_data_json')->textarea(['rows' => 8, 'class' => 'form-control']);
        } catch (Throwable $throwable) {
            Yii::error(AppHelper::throwableFormatter($throwable), 'CouponProductCrudController:_form:notValidJson');
        }
    }
    ?>

    <?php // $form->field($model, 'cel_created_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

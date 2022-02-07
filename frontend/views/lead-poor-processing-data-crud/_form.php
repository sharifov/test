<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\leadPoorProcessingData\entity\LeadPoorProcessingData */
/* @var $form ActiveForm */
?>

<div class="lead-poor-processing-data-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'lppd_enabled')->dropDownList([1 => 'Yes', 0 => 'No']) ?>

        <?= $form->field($model, 'lppd_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'lppd_description')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'lppd_minute')->textInput() ?>

        <?php
        try {
            $model->lppd_params_json = \frontend\helpers\JsonHelper::encode($model->lppd_params_json);
            echo $form->field($model, 'lppd_params_json')->widget(
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
            $model->lppd_params_json = '{}';
            echo $form->field($model, 'lppd_params_json')->textarea(['rows' => 6]);
        }
        ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

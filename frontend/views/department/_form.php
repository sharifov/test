<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Department */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="department-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="col-md-4">

    <?php //= $form->field($model, 'dep_id')->textInput() ?>

    <?php //= $form->field($model, 'dep_key')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'dep_name')->textInput(['maxlength' => true]) ?>

        <?php
        try {
            echo $form->field($model, 'dep_params')->widget(
                \kdn\yii2\JsonEditor::class,
                [
                    'clientOptions' => [
                        'modes' => ['code', 'form', 'tree', 'view'], //'text',
                        'mode' => 'tree'
                    ],
                    //'collapseAll' => ['view'],
                    'expandAll' => ['tree', 'form'],
                ]
            );
        } catch (Exception $exception) {
            echo $form->field($model, 'dep_params')->textarea(['rows' => 5]);
        }

        ?>


    <?php //= $form->field($model, 'dep_updated_user_id')->textInput() ?>

    <?php //= $form->field($model, 'dep_updated_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

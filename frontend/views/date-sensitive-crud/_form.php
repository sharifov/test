<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\DateSensitive */
/* @var $form yii\widgets\ActiveForm */

?>


<div class="date-sensitive-form row">

    <div class="col-md-6">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'da_key')->textInput() ?>

        <?= $form->field($model, 'da_name')->textInput() ?>

        <?php
        try {
            echo $form->field($model, 'da_source')->widget(
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
            echo $form->field($model, 'da_source')->textarea(['rows' => 5]);
        }

        ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Project */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-group-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="col-md-6">
        <?php
            try {
                echo $form->field($model, 'custom_data')->widget(
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
                echo $form->field($model, 'custom_data')->textarea(['rows' => 6]);
            }

        ?>


        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

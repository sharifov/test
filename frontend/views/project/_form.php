<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Project */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-6">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'link')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'api_key')->textInput(['maxlength' => true]) ?>

        <?php //= $form->field($model, 'contact_info')->textarea(['rows' => 6]) ?>

        <?php

        try {
            echo $form->field($model, 'contact_info')->widget(
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
            echo $form->field($model, 'contact_info')->textarea(['rows' => 6]);
        }

        ?>


        <?= $form->field($model, 'closed')->checkbox() ?>

    </div>
    <div class="col-md-6">


    <?php /*= $form->field($model, 'last_update')->textInput()*/ ?>


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

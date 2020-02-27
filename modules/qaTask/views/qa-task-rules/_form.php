<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\qaTask\src\entities\qaTaskRules\QaTaskRules */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="qa-task-rules-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'tr_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'tr_description')->textInput(['maxlength' => true]) ?>

        <?php

        try {
            echo $form->field($model, 'tr_parameters')->widget(
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
            echo $form->field($model, 'tr_parameters')->textarea(['rows' => 6]);
        }

        ?>

        <?= $form->field($model, 'tr_enabled')->dropDownList([1 => 'Yes', 0 => 'No']) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

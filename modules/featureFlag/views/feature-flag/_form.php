<?php

use modules\featureFlag\src\entities\FeatureFlag;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\featureFlag\src\entities\FeatureFlag */
/* @var $form ActiveForm */
?>

<div class="feature-flag-form">


    <div class="col-md-6">

        <?php $form = ActiveForm::begin(); ?>

        <?php //= $form->field($model, 'ff_key')->textInput(['maxlength' => true]) ?>

        <?php //= $form->field($model, 'ff_name')->textInput(['maxlength' => true]) ?>

        <?php //= $form->field($model, 'ff_type')->textInput(['maxlength' => true]) ?>

        <?php

        if ($model->ff_type === FeatureFlag::TYPE_STRING) {
            echo $form->field($model, 'ff_value')->textInput();
        } elseif ($model->ff_type === FeatureFlag::TYPE_BOOL) {
            echo $form->field($model, 'ff_value')->checkbox();//->label($model->ff_name);
        } elseif ($model->ff_type === FeatureFlag::TYPE_INT) {
            echo $form->field($model, 'ff_value')->input('number');//->label($model->ff_name);
        } elseif ($model->ff_type === FeatureFlag::TYPE_DOUBLE) {
            echo $form->field($model, 'ff_value')->input('number', ['step' => 0.01]);//->label($model->ff_name);
        } elseif ($model->ff_type === FeatureFlag::TYPE_ARRAY) {
            try {
                echo $form->field($model, 'ff_value')->widget(
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
                echo $form->field($model, 'ff_value')->textarea(['rows' => 5]);//->label($model->ff_name);
            }
        } else {
//                echo $form->field($model, 'ff_value')->textInput(['maxlength' => true])->label($model->ff_name);
            echo $form->field($model, 'ff_value')->widget(
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
        }
        ?>

        <?php //= $form->field($model, 'ff_value')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ff_description')->textarea(['rows' => 3]) ?>

        <?= $form->field($model, 'ff_category')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ff_enable_type')->dropDownList(FeatureFlag::getEnableTypeList(), ['prompt' => '-']) ?>

        <?= $form->field($model, 'ff_attributes')->textInput() ?>

        <?= $form->field($model, 'ff_condition')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save Value', ['class' => 'btn btn-success']) ?>
        </div>
    






        

        <?php ActiveForm::end(); ?>

    </div>

</div>

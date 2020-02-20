<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \modules\product\src\entities\productType\ProductType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-type-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'pt_id')->textInput() ?>

        <?= $form->field($model, 'pt_key')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'pt_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'pt_service_fee_percent')->input('number', ['step' => 0.01, 'min' => 0, 'max' => 100]) ?>


        <?= $form->field($model, 'pt_description')->textarea(['rows' => 6]) ?>


        <?php

        try {
            echo $form->field($model, 'pt_settings')->widget(
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
            echo $form->field($model, 'pt_settings')->textarea(['rows' => 6]);
        }

        ?>


        <?= $form->field($model, 'pt_enabled')->checkbox() ?>

        <?php //= $form->field($model, 'pt_created_dt')->textInput() ?>

        <?php //= $form->field($model, 'pt_updated_dt')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
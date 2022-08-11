<?php

use dosamigos\ckeditor\CKEditor;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\InfoBlock */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="info-block-form row">
    <div class="col-md-6">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ib_title')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ib_key')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ib_description')->widget(CKEditor::class, [
            'options' => [
                'rows' => 2,
                'readonly' => false
            ],
            'preset' => 'custom',
            'clientOptions' => [
                'height' => 200,
                'removeButtons' => 'Subscript,Superscript,Flash,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe',
            ]
        ]) ?>

        <?= $form->field($model, 'ib_enabled')->checkbox() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>

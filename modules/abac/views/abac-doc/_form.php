<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\abac\src\entities\abacDoc\AbacDoc */
/* @var $form ActiveForm */
?>

<div class="abac-doc-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ad_file')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ad_line')->textInput() ?>

        <?= $form->field($model, 'ad_subject')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ad_object')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ad_action')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ad_description')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

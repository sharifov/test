<?php

use sales\model\emailList\entity\EmailList;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model EmailList */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="email-list-form">
    <div class="row">
        <div class="col-md-4">

            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'el_email')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'el_title')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'el_enabled')->dropDownList([1 => 'Yes', 0 => 'No'], ['prompt' => 'Select...']) ?>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>

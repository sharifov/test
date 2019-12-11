<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserGroup */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-group-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="col-md-6">

        <?= $form->field($model, 'ug_key')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ug_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ug_description')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ug_processing_fee')->textInput(['type' => 'number','maxlength' => true]) ?>

        <?= $form->field($model, 'ug_disable')->checkbox() ?>

        <?= $form->field($model, 'ug_on_leaderboard')->checkbox() ?>

        <?//= $form->field($model, 'ug_updated_dt')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

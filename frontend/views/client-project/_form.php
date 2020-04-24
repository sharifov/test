<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ClientProject */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-project-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cp_client_id')->textInput() ?>

    <?= $form->field($model, 'cp_project_id')->textInput() ?>

    <?= $form->field($model, 'cp_created_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

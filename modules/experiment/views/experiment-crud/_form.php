<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\experiment\models\Experiment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="experiment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ex_code')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\requestControl\models\Rule */
/* @var $form ActiveForm */
?>

<div class="request-control-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'type')->textInput() ?>

        <?= $form->field($model, 'subject')->textInput() ?>

        <?= $form->field($model, 'local')->textInput() ?>

        <?= $form->field($model, 'global')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\callNote\entity\CallNote */
/* @var $form ActiveForm */
?>

<div class="call-note-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'cn_call_id')->textInput() ?>

        <?= $form->field($model, 'cn_note')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cn_created_dt')->textInput() ?>

        <?= $form->field($model, 'cn_updated_dt')->textInput() ?>

        <?= $form->field($model, 'cn_created_user_id')->textInput() ?>

        <?= $form->field($model, 'cn_updated_user_id')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

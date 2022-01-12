<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\user\userFeedback\entity\UserFeedback */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-feedback-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'uf_id')->textInput() ?>

    <?= $form->field($model, 'uf_type_id')->textInput() ?>

    <?= $form->field($model, 'uf_status_id')->textInput() ?>

    <?= $form->field($model, 'uf_title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'uf_message')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'uf_data_json')->textInput() ?>

    <?= $form->field($model, 'uf_created_dt')->textInput() ?>

    <?= $form->field($model, 'uf_updated_dt')->textInput() ?>

    <?= $form->field($model, 'uf_created_user_id')->textInput() ?>

    <?= $form->field($model, 'uf_updated_user_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

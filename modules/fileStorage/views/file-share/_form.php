<?php

use sales\widgets\DateTimePicker;
use sales\widgets\UserSelect2Widget;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileShare\FileShare */
/* @var $form ActiveForm */
?>

<div class="file-share-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'fsh_fs_id')->textInput() ?>

        <?= $form->field($model, 'fsh_code')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'fsh_expired_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'fsh_created_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'fsh_created_user_id')->widget(UserSelect2Widget::class, [
            'data' => $model->fsh_created_user_id ? [
                $model->fsh_created_user_id => $model->createdUser->nickname
            ] : [],
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

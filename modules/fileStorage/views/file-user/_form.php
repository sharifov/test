<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;
use sales\widgets\UserSelect2Widget;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileUser\FileUser */
/* @var $form ActiveForm */
?>

<div class="file-user-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'fus_fs_id')->textInput() ?>

        <?= $form->field($model, 'fus_user_id')->widget(UserSelect2Widget::class, [
            'data' => $model->fus_user_id ? [
                $model->fus_user_id => $model->user->username
            ] : [],
        ]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

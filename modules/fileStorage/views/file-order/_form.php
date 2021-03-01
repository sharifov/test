<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileOrder\FileOrder */
/* @var $form ActiveForm */
?>

<div class="file-client-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'fo_fs_id')->textInput() ?>

        <?= $form->field($model, 'fo_or_id')->textInput() ?>

        <?= $form->field($model, 'fo_pq_id')->textInput() ?>

        <?= $form->field($model, 'fo_category_id')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

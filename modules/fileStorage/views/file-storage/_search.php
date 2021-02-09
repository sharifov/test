<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileStorage\search\FileStorageSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="file-storage-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'fs_id') ?>

    <?= $form->field($model, 'fs_uid') ?>

    <?= $form->field($model, 'fs_mime_type') ?>

    <?= $form->field($model, 'fs_name') ?>

    <?= $form->field($model, 'fs_title') ?>

    <?php // echo $form->field($model, 'fs_path') ?>

    <?php // echo $form->field($model, 'fs_size') ?>

    <?php // echo $form->field($model, 'fs_private')->checkbox() ?>

    <?php // echo $form->field($model, 'fs_expired_dt') ?>

    <?php // echo $form->field($model, 'fs_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

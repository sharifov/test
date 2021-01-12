<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileShare\search\FileShareSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="file-share-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'fsh_id') ?>

    <?= $form->field($model, 'fsh_fs_id') ?>

    <?= $form->field($model, 'fsh_code') ?>

    <?= $form->field($model, 'fsh_expired_dt') ?>

    <?= $form->field($model, 'fsh_created_dt') ?>

    <?php // echo $form->field($model, 'fsh_created_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

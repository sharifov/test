<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileLog\search\FileLogSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="file-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'fl_id') ?>

    <?= $form->field($model, 'fl_fs_id') ?>

    <?= $form->field($model, 'fl_fsh_id') ?>

    <?= $form->field($model, 'fl_type_id') ?>

    <?= $form->field($model, 'fl_created_dt') ?>

    <?php // echo $form->field($model, 'fl_ip_address') ?>

    <?php // echo $form->field($model, 'fl_user_agent') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

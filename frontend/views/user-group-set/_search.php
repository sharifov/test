<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\UserGroupSetSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-group-set-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ugs_id') ?>

    <?= $form->field($model, 'ugs_name') ?>

    <?= $form->field($model, 'ugs_enabled') ?>

    <?= $form->field($model, 'ugs_created_dt') ?>

    <?= $form->field($model, 'ugs_updated_dt') ?>

    <?php // echo $form->field($model, 'ugs_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

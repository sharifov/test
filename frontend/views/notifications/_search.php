<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\NotificationsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="notifications-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'n_id') ?>

    <?= $form->field($model, 'n_user_id') ?>

    <?= $form->field($model, 'n_type_id') ?>

    <?= $form->field($model, 'n_title') ?>

    <?= $form->field($model, 'n_message') ?>

    <?php // echo $form->field($model, 'n_new')->checkbox() ?>

    <?php // echo $form->field($model, 'n_deleted')->checkbox() ?>

    <?php // echo $form->field($model, 'n_popup')->checkbox() ?>

    <?php // echo $form->field($model, 'n_popup_show')->checkbox() ?>

    <?php // echo $form->field($model, 'n_read_dt') ?>

    <?php // echo $form->field($model, 'n_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('notifications', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('notifications', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

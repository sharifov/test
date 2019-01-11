<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\UserConnectionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-connection-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'uc_id') ?>

    <?= $form->field($model, 'uc_connection_id') ?>

    <?= $form->field($model, 'uc_user_id') ?>

    <?= $form->field($model, 'uc_lead_id') ?>

    <?= $form->field($model, 'uc_user_agent') ?>

    <?php // echo $form->field($model, 'uc_controller_id') ?>

    <?php // echo $form->field($model, 'uc_action_id') ?>

    <?php // echo $form->field($model, 'uc_page_url') ?>

    <?php // echo $form->field($model, 'uc_ip') ?>

    <?php // echo $form->field($model, 'uc_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

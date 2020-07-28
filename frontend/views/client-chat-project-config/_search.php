<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\entity\projectConfig\search\ClientChatProjectConfigSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-chat-project-config-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ccpc_project_id') ?>

    <?= $form->field($model, 'ccpc_params_json') ?>

    <?= $form->field($model, 'ccpc_theme_json') ?>

    <?= $form->field($model, 'ccpc_registration_json') ?>

    <?= $form->field($model, 'ccpc_settings_json') ?>

    <?php // echo $form->field($model, 'ccpc_enabled') ?>

    <?php // echo $form->field($model, 'ccpc_created_user_id') ?>

    <?php // echo $form->field($model, 'ccpc_updated_user_id') ?>

    <?php // echo $form->field($model, 'ccpc_created_dt') ?>

    <?php // echo $form->field($model, 'ccpc_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

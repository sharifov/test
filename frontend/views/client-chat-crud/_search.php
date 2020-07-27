<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\entity\search\ClientChatSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="client-chat-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cch_id') ?>

    <?= $form->field($model, 'cch_rid') ?>

    <?= $form->field($model, 'cch_ccr_id') ?>

    <?= $form->field($model, 'cch_title') ?>

    <?= $form->field($model, 'cch_description') ?>

    <?php // echo $form->field($model, 'cch_project_id') ?>

    <?php // echo $form->field($model, 'cch_dep_id') ?>

    <?php // echo $form->field($model, 'cch_channel_id') ?>

    <?php // echo $form->field($model, 'cch_client_id') ?>

    <?php // echo $form->field($model, 'cch_owner_user_id') ?>

    <?php // echo $form->field($model, 'cch_note') ?>

    <?php // echo $form->field($model, 'cch_status_id') ?>

    <?php // echo $form->field($model, 'cch_ip') ?>

    <?php // echo $form->field($model, 'cch_ua') ?>

    <?php // echo $form->field($model, 'cch_language_id') ?>

    <?php // echo $form->field($model, 'cch_created_dt') ?>

    <?php // echo $form->field($model, 'cch_updated_dt') ?>

    <?php // echo $form->field($model, 'cch_created_user_id') ?>

    <?php // echo $form->field($model, 'cch_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

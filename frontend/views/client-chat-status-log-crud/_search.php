<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatStatusLog\entity\search\ClientChatStatusLog */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="client-chat-status-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'csl_id') ?>

    <?= $form->field($model, 'csl_cch_id') ?>

    <?= $form->field($model, 'csl_from_status') ?>

    <?= $form->field($model, 'csl_to_status') ?>

    <?= $form->field($model, 'csl_start_dt') ?>

    <?php // echo $form->field($model, 'csl_end_dt') ?>

    <?php // echo $form->field($model, 'csl_owner_id') ?>

    <?php // echo $form->field($model, 'csl_description') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

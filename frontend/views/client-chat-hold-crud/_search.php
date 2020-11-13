<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatHold\entity\ClientChatHoldSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-chat-hold-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cchd_id') ?>

    <?= $form->field($model, 'cchd_cch_id') ?>

    <?= $form->field($model, 'cchd_cch_status_log_id') ?>

    <?= $form->field($model, 'cchd_deadline_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

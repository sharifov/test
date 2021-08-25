<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\client\notifications\client\entity\search\ClientNotificationSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="client-notification-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cn_id') ?>

    <?= $form->field($model, 'cn_client_id') ?>

    <?= $form->field($model, 'cn_notification_type_id') ?>

    <?= $form->field($model, 'cn_object_id') ?>

    <?= $form->field($model, 'cn_communication_type_id') ?>

    <?php // echo $form->field($model, 'cn_communication_object_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

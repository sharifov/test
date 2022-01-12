<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\smsSubscribe\entity\SmsSubscribeSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="sms-subscribe-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ss_id') ?>

    <?= $form->field($model, 'ss_cpl_id') ?>

    <?= $form->field($model, 'ss_project_id') ?>

    <?= $form->field($model, 'ss_status_id') ?>

    <?= $form->field($model, 'ss_created_dt') ?>

    <?php // echo $form->field($model, 'ss_updated_dt') ?>

    <?php // echo $form->field($model, 'ss_deadline_dt') ?>

    <?php // echo $form->field($model, 'ss_created_user_id') ?>

    <?php // echo $form->field($model, 'ss_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

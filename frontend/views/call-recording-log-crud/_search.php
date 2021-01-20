<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\callRecordingLog\entity\search\CallRecordingLogSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="call-recording-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'crl_id') ?>

    <?= $form->field($model, 'crl_call_sid') ?>

    <?= $form->field($model, 'crl_user_id') ?>

    <?= $form->field($model, 'crl_created_dt') ?>

    <?= $form->field($model, 'crl_year') ?>

    <?php // echo $form->field($model, 'crl_month') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

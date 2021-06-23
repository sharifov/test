<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\callTerminateLog\entity\CallTerminateLogSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="call-terminate-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ctl_id') ?>

    <?= $form->field($model, 'ctl_call_phone_number') ?>

    <?= $form->field($model, 'ctl_call_status_id') ?>

    <?= $form->field($model, 'ctl_project_id') ?>

    <?= $form->field($model, 'ctl_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

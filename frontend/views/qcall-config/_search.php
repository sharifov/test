<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\QcallConfigSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="qcall-config-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'qc_status_id') ?>

    <?= $form->field($model, 'qc_call_att') ?>

    <?= $form->field($model, 'qc_client_time_enable') ?>

    <?= $form->field($model, 'qc_time_from') ?>

    <?= $form->field($model, 'qc_time_to') ?>

    <?php // echo $form->field($model, 'qc_created_dt') ?>

    <?php // echo $form->field($model, 'qc_updated_dt') ?>

    <?php // echo $form->field($model, 'qc_created_user_id') ?>

    <?php // echo $form->field($model, 'qc_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

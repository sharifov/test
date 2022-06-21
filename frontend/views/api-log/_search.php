<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\ApiLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="api-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'al_id') ?>

    <?= $form->field($model, 'al_request_data') ?>

    <?= $form->field($model, 'al_request_dt') ?>

    <?= $form->field($model, 'al_response_data') ?>

    <?= $form->field($model, 'al_response_dt') ?>

    <?php // echo $form->field($model, 'al_ip_address') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

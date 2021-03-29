<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderRequest\search\OrderRequestSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="order-request-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'orr_id') ?>

    <?= $form->field($model, 'orr_request_data_json') ?>

    <?= $form->field($model, 'orr_response_data_json') ?>

    <?= $form->field($model, 'orr_source_type_id') ?>

    <?= $form->field($model, 'orr_response_type_id') ?>

    <?php // echo $form->field($model, 'orr_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderEmail\search\OrderEmailSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="order-email-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'oe_id') ?>

    <?= $form->field($model, 'oe_order_id') ?>

    <?= $form->field($model, 'oe_email_id') ?>

    <?= $form->field($model, 'oe_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

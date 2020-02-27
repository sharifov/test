<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderTips\search\OrderTipsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-tips-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

<!--    --><?//= $form->field($model, 'ot_id') ?>

    <?= $form->field($model, 'ot_order_id') ?>

    <?= $form->field($model, 'ot_client_amount') ?>

    <?= $form->field($model, 'ot_amount') ?>

    <?= $form->field($model, 'ot_user_profit') ?>

	<?= $form->field($model, 'ot_user_profit_percent') ?>

    <?php // echo $form->field($model, 'ot_description') ?>

    <?php // echo $form->field($model, 'ot_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

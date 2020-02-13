<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\user\entity\profit\search\UserProfitSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-profit-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'up_id') ?>

    <?= $form->field($model, 'up_user_id') ?>

    <?= $form->field($model, 'up_lead_id') ?>

    <?= $form->field($model, 'up_order_id') ?>

    <?= $form->field($model, 'up_product_quote_id') ?>

    <?php // echo $form->field($model, 'up_percent') ?>

    <?php // echo $form->field($model, 'up_profit') ?>

    <?php // echo $form->field($model, 'up_split_percent') ?>

    <?php // echo $form->field($model, 'up_amount') ?>

    <?php // echo $form->field($model, 'up_status_id') ?>

    <?php // echo $form->field($model, 'up_created_dt') ?>

    <?php // echo $form->field($model, 'up_updated_dt') ?>

    <?php // echo $form->field($model, 'up_payroll_id') ?>

    <?php // echo $form->field($model, 'up_type_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

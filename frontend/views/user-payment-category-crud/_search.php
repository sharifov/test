<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\user\entity\paymentCategory\search\UserPaymentCategorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-payment-category-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'upc_id') ?>

    <?= $form->field($model, 'upc_name') ?>

    <?= $form->field($model, 'upc_description') ?>

    <?= $form->field($model, 'upc_enabled') ?>

    <?= $form->field($model, 'upc_created_user_id') ?>

    <?php // echo $form->field($model, 'upc_updated_user_id') ?>

    <?php // echo $form->field($model, 'upc_created_dt') ?>

    <?php // echo $form->field($model, 'upc_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

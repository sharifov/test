<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \modules\offer\src\entities\offerProduct\search\OfferProductCrudSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="offer-product-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'op_offer_id') ?>

    <?= $form->field($model, 'op_product_quote_id') ?>

    <?= $form->field($model, 'op_created_user_id') ?>

    <?= $form->field($model, 'op_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

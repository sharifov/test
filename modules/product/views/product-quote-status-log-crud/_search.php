<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteStatusLog\search\ProductQuoteStatusLogCrudSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-quote-status-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'pqsl_id') ?>

    <?= $form->field($model, 'pqsl_product_quote_id') ?>

    <?= $form->field($model, 'pqsl_start_status_id') ?>

    <?= $form->field($model, 'pqsl_end_status_id') ?>

    <?= $form->field($model, 'pqsl_start_dt') ?>

    <?php // echo $form->field($model, 'pqsl_end_dt') ?>

    <?php // echo $form->field($model, 'pqsl_duration') ?>

    <?php // echo $form->field($model, 'pqsl_description') ?>

    <?php // echo $form->field($model, 'pqsl_owner_user_id') ?>

    <?php // echo $form->field($model, 'pqsl_created_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

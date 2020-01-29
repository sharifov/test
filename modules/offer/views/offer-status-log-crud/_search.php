<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\offer\src\entities\offerStatusLog\search\OfferStatusLogCrudSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="offer-status-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'osl_id') ?>

    <?= $form->field($model, 'osl_product_quote_id') ?>

    <?= $form->field($model, 'osl_start_status_id') ?>

    <?= $form->field($model, 'osl_end_status_id') ?>

    <?= $form->field($model, 'osl_start_dt') ?>

    <?php // echo $form->field($model, 'osl_end_dt') ?>

    <?php // echo $form->field($model, 'osl_duration') ?>

    <?php // echo $form->field($model, 'osl_description') ?>

    <?php // echo $form->field($model, 'osl_owner_user_id') ?>

    <?php // echo $form->field($model, 'osl_created_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

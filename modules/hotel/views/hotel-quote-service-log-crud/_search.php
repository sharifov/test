<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\search\HotelQuoteServiceLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hotel-quote-service-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'hqsl_id') ?>
    <?= $form->field($model, 'hqsl_hotel_quote_id') ?>
    <?= $form->field($model, 'hqsl_action_type_id') ?>
    <?= $form->field($model, 'hqsl_status_id') ?>
    <?= $form->field($model, 'hqsl_message') ?>
    <?php // echo $form->field($model, 'hqsl_created_user_id') ?>
    <?php // echo $form->field($model, 'hqsl_created_dt') ?>
    <?php // echo $form->field($model, 'hqsl_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

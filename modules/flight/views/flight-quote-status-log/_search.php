<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\search\FlightQuoteStatusLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="flight-quote-status-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'qsl_id') ?>

    <?= $form->field($model, 'qsl_created_user_id') ?>

    <?= $form->field($model, 'qsl_flight_quote_id') ?>

    <?= $form->field($model, 'qsl_status_id') ?>

    <?= $form->field($model, 'qsl_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

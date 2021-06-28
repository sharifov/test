<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\flight\src\entities\flightQuoteLabel\FlightQuoteLabel */
/* @var $form ActiveForm */
?>

<div class="flight-quote-label-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'fql_quote_id')->textInput() ?>

        <?= $form->field($model, 'fql_label_key')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

<?php

use common\models\QuotePrice;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Currency;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\QuoteSegmentBaggageCharge */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="quote-segment-baggage-charge-form row">

    <div class="col-md-6">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'qsbc_pax_code')->dropDownList(QuotePrice::PASSENGER_TYPE_LIST) ?>

        <?= $form->field($model, 'qsbc_segment_id')->textInput() ?>

        <?= $form->field($model, 'qsbc_first_piece')->textInput() ?>

        <?= $form->field($model, 'qsbc_last_piece')->textInput() ?>

        <?= $form->field($model, 'qsbc_price')->textInput() ?>

        <?= $form->field($model, 'qsbc_currency')->widget(Select2::class, [
            'data' => Currency::getList(),
            'size' => Select2::SIZE_SMALL
        ]) ?>

        <?= $form->field($model, 'qsbc_max_weight')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'qsbc_max_size')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>

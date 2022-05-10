<?php

use common\models\Airline;
use common\models\QuotePrice;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\QuoteSegmentBaggage */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="quote-segment-baggage-form row">
    <div class="col-md-6">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'qsb_pax_code')->dropDownList(QuotePrice::PASSENGER_TYPE_LIST) ?>

        <?= $form->field($model, 'qsb_segment_id')->textInput() ?>

        <?= $form->field($model, 'qsb_airline_code')
            ->widget(Select2::class, [
                'data' => Airline::getAirlinesMapping(true),
                'size' => Select2::SIZE_SMALL
            ]) ?>


        <?= $form->field($model, 'qsb_allow_pieces')->textInput() ?>

        <?= $form->field($model, 'qsb_allow_weight')->textInput() ?>

        <?= $form->field($model, 'qsb_allow_unit')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'qsb_allow_max_weight')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'qsb_allow_max_size')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'qsb_carry_one')->checkbox() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>

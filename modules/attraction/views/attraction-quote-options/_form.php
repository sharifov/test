<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\attraction\models\AttractionQuoteOptions */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="attraction-quote-options-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'atqo_attraction_quote_id')->textInput() ?>

    <?= $form->field($model, 'atqo_answered_value')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'atqo_label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'atqo_is_answered')->textInput() ?>

    <?= $form->field($model, 'atqo_answer_formatted_text')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

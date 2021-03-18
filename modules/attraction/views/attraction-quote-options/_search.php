<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\attraction\models\search\AttractionQuoteOptionsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="attraction-quote-options-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'atqo_id') ?>

    <?= $form->field($model, 'atqo_attraction_quote_id') ?>

    <?= $form->field($model, 'atqo_answered_value') ?>

    <?= $form->field($model, 'atqo_label') ?>

    <?= $form->field($model, 'atqo_is_answered') ?>

    <?php // echo $form->field($model, 'atqo_answer_formatted_text') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

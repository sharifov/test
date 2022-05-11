<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\QuoteTripSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="quote-trip-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'qt_id') ?>

    <?= $form->field($model, 'qt_duration') ?>

    <?= $form->field($model, 'qt_key') ?>

    <?= $form->field($model, 'qt_quote_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\emailQuote\entity\EmailQuoteSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="email-quote-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'eq_id') ?>

    <?= $form->field($model, 'eq_email_id') ?>

    <?= $form->field($model, 'eq_quote_id') ?>

    <?= $form->field($model, 'eq_created_dt') ?>

    <?= $form->field($model, 'eq_created_by') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

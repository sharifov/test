<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\cruise\src\entity\cruiseQuote\search\CruiseQuoteSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="cruise-quote-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'crq_id') ?>

    <?= $form->field($model, 'crq_hash_key') ?>

    <?= $form->field($model, 'crq_product_quote_id') ?>

    <?= $form->field($model, 'crq_cruise_id') ?>

    <?= $form->field($model, 'crq_data_json') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

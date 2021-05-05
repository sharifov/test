<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\attraction\models\search\AttractionQuotePricingCategorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="attraction-quote-pricing-category-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'atqpc_id') ?>

    <?= $form->field($model, 'atqpc_attraction_quote_id') ?>

    <?= $form->field($model, 'atqpc_category_id') ?>

    <?= $form->field($model, 'atqpc_label') ?>

    <?= $form->field($model, 'atqpc_min_age') ?>

    <?php // echo $form->field($model, 'atqpc_max_age') ?>

    <?php // echo $form->field($model, 'atqpc_min_participants') ?>

    <?php // echo $form->field($model, 'atqpc_max_participants') ?>

    <?php // echo $form->field($model, 'atqpc_quantity') ?>

    <?php // echo $form->field($model, 'atqpc_price') ?>

    <?php // echo $form->field($model, 'atqpc_currency') ?>

    <?php // echo $form->field($model, 'atqpc_system_mark_up') ?>

    <?php // echo $form->field($model, 'atqpc_agent_mark_up') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\attraction\models\AttractionQuotePricingCategory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="attraction-quote-pricing-category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'atqpc_attraction_quote_id')->textInput() ?>

    <?= $form->field($model, 'atqpc_category_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'atqpc_label')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'atqpc_min_age')->textInput() ?>

    <?= $form->field($model, 'atqpc_max_age')->textInput() ?>

    <?= $form->field($model, 'atqpc_min_participants')->textInput() ?>

    <?= $form->field($model, 'atqpc_max_participants')->textInput() ?>

    <?= $form->field($model, 'atqpc_quantity')->textInput() ?>

    <?= $form->field($model, 'atqpc_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'atqpc_currency')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'atqpc_system_mark_up')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'atqpc_agent_mark_up')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeStatus;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeDecisionType;
use sales\widgets\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteChange\ProductQuoteChange */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-quote-change-form">
    <div class="row">
    <div class="col-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'pqc_pq_id')->textInput() ?>

        <?= $form->field($model, 'pqc_case_id')->textInput() ?>

        <?= $form->field($model, 'pqc_decision_user')->widget(\sales\widgets\UserSelect2Widget::class, [
            'data' => $model->pqc_decision_user ? [
                $model->pqc_decision_user => $model->pqcDecisionUser->username
            ] : [],
        ]) ?>

        <?= $form->field($model, 'pqc_status_id')->dropDownList(ProductQuoteChangeStatus::getList(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'pqc_is_automate')->checkbox() ?>

      <?= $form->field($model, 'pqc_decision_type_id')->dropDownList(ProductQuoteChangeDecisionType::getList(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'pqc_created_dt')->widget(DateTimePicker::class, []) ?>

        <?= $form->field($model, 'pqc_updated_dt')->widget(DateTimePicker::class, []) ?>

        <?= $form->field($model, 'pqc_decision_dt')->widget(DateTimePicker::class, [])?>

        <?= $form->field($model, 'pqc_type_id')->dropDownList(ProductQuoteChange::TYPE_LIST, ['prompt' => '---']) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
    </div>
</div>

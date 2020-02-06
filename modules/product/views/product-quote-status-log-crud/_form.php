<?php

use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuote\ProductQuoteStatusAction;
use sales\access\ListsAccess;
use sales\auth\Auth;
use sales\widgets\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteStatusLog\ProductQuoteStatusLog */
/* @var $form yii\widgets\ActiveForm */

$list = new ListsAccess(Auth::id());

?>

<div class="product-quote-status-log-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'pqsl_product_quote_id')->textInput() ?>

        <?= $form->field($model, 'pqsl_start_status_id')->dropDownList(ProductQuoteStatus::getList(), ['prompt' => 'Select status']) ?>

        <?= $form->field($model, 'pqsl_end_status_id')->dropDownList(ProductQuoteStatus::getList(), ['prompt' => 'Select status']) ?>

        <?= $form->field($model, 'pqsl_start_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'pqsl_end_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'pqsl_duration')->textInput() ?>

        <?= $form->field($model, 'pqsl_description')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'pqsl_action_id')->dropDownList(ProductQuoteStatusAction::getList(), ['prompt' => 'Select action']) ?>

        <?= $form->field($model, 'pqsl_owner_user_id')->dropDownList($list->getEmployees(), ['prompt' => 'Select user']) ?>

        <?= $form->field($model, 'pqsl_created_user_id')->dropDownList($list->getEmployees(), ['prompt' => 'Select user']) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

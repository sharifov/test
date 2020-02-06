<?php

use modules\product\src\entities\productQuote\ProductQuoteStatus;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \modules\product\src\entities\productQuote\ProductQuote */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-quote-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'pq_gid')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'pq_clone_id')->textInput() ?>

        <?= $form->field($model, 'pq_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'pq_product_id')->textInput() ?>

        <?= $form->field($model, 'pq_order_id')->textInput() ?>

        <?= $form->field($model, 'pq_description')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'pq_status_id')->dropDownList(ProductQuoteStatus::getList(), ['prompt' => 'Select status']) ?>

        <?= $form->field($model, 'pq_price')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'pq_origin_price')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'pq_client_price')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'pq_service_fee_sum')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'pq_origin_currency')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'pq_client_currency')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'pq_origin_currency_rate')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'pq_client_currency_rate')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'pq_owner_user_id')->textInput() ?>

        <? /*= $form->field($model, 'pq_created_user_id')->textInput() ?>

    <?= $form->field($model, 'pq_updated_user_id')->textInput() ?>

    <?= $form->field($model, 'pq_created_dt')->textInput() ?>

    <?= $form->field($model, 'pq_updated_dt')->textInput()*/ ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

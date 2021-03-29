<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderRequest\OrderRequest */
/* @var $form ActiveForm */
?>

<div class="order-request-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'orr_request_data_json')->textInput() ?>

        <?= $form->field($model, 'orr_response_data_json')->textInput() ?>

        <?= $form->field($model, 'orr_source_type_id')->textInput() ?>

        <?= $form->field($model, 'orr_response_type_id')->textInput() ?>

        <?= $form->field($model, 'orr_created_dt')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

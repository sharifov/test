<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderData\OrderData */
/* @var $form ActiveForm */
?>

<div class="order-data-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'od_order_id')->textInput() ?>

        <?= $form->field($model, 'od_display_uid')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'od_source_cid')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'od_created_by')->textInput() ?>

        <?= $form->field($model, 'od_updated_by')->textInput() ?>

        <?= $form->field($model, 'od_created_dt')->textInput() ?>

        <?= $form->field($model, 'od_updated_dt')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

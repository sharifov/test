<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderTips\OrderTips */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-tips-form">

    <?php \yii\widgets\Pjax::begin() ?>

    <?php $form = ActiveForm::begin(['options' => ['data-pjax' => 1]]); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->errorSummary($model) ?>

            <?= $form->field($model, 'ot_order_id')->input('number', ['step' => 1]) ?>

            <?= $form->field($model, 'ot_client_amount')->input('number', ['maxlength' => true, 'step' => 0.01]) ?>

            <?= $form->field($model, 'ot_amount')->input('number', ['maxlength' => true, 'step' => 0.01]) ?>

            <?= $form->field($model, 'ot_user_profit')->input('number', ['maxlength' => true, 'step' => 0.01]) ?>

            <?= $form->field($model, 'ot_description')->textarea(['maxlength' => true]) ?>

        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php \yii\widgets\Pjax::end() ?>

</div>

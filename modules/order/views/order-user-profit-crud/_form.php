<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderUserProfit\OrderUserProfit */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-user-profit-form">

    <?php \yii\widgets\Pjax::begin() ?>

    <?php $form = ActiveForm::begin(['options' => ['data-pjax' => 1]]); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->errorSummary($model) ?>

            <?= $form->field($model, 'oup_order_id')->input('number') ?>

            <?= $form->field($model, 'oup_user_id')->dropDownList(\common\models\Employee::getList(), ['prompt' => '--']) ?>

            <?= $form->field($model, 'oup_percent')->input('number') ?>

<!--            --><?//= $form->field($model, 'oup_amount')->input('number', ['maxlength' => true, 'step' => 0.01]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php \yii\widgets\Pjax::end() ?>

</div>

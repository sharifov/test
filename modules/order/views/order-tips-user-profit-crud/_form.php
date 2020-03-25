<?php

use common\models\Employee;
use modules\order\src\entities\orderTipsUserProfit\OrderTipsUserProfit;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderTipsUserProfit\OrderTipsUserProfit */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-tips-user-profit-form">
    <?php Pjax::begin() ?>

    <?php $form = ActiveForm::begin(['options' => ['data-pjax' => 1]]); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->errorSummary($model) ?>

            <?= $form->field($model, 'otup_order_id')->input('number', ['step' => 1]) ?>

            <?= $form->field($model, 'otup_user_id')->dropDownList(Employee::getList(), ['prompt' => '---']) ?>

            <?= $form->field($model, 'otup_percent')->input('number', ['step' => 1, 'maxlength' => true, 'max' => OrderTipsUserProfit::MAX_PERCENT, 'min' => OrderTipsUserProfit::MIN_PERCENT]) ?>

        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php Pjax::end() ?>

</div>

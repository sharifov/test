<?php

use frontend\models\form\OrderForm;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model OrderForm */
/* @var $form yii\bootstrap4\ActiveForm */

$pjaxId = 'pjax-order-form';
?>

<div class="order-form">

    <script>
        pjaxOffFormSubmit('#<?=$pjaxId?>');
    </script>
    <?php \yii\widgets\Pjax::begin(['id' => $pjaxId, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
    <?php
    $form = ActiveForm::begin([
        'options' => ['data-pjax' => true],
        'action' => ['/order/update-ajax', 'id' => $model->or_id],
        'method' => 'post'
    ]);
    ?>

    <?php echo $form->errorSummary($model) ?>

    <?= $form->field($model, 'or_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'or_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'or_status_id')->dropDownList(\common\models\Order::getStatusList(), ['prompt' => '---']) ?>

    <?= $form->field($model, 'or_pay_status_id')->dropDownList(\common\models\Order::getPayStatusList(), ['prompt' => '---']) ?>

    <div class="form-group text-center">
        <?= Html::submitButton('<i class="fa fa-save"></i> Save order', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php \yii\widgets\Pjax::end(); ?>

</div>
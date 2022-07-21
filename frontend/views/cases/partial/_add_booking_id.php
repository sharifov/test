<?php

use src\model\cases\useCases\cases\addBookingId\AddBookingIdForm;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model AddBookingIdForm */
/* @var $form yii\widgets\ActiveForm */

?>
<?php Pjax::begin(['id' => 'pjax-cases-add-booking-id-form']); ?>
<div class="cases-change-status">
    <?php $form = ActiveForm::begin([
        'action' => ['/cases/ajax-add-booking-id', 'gid' => $model->getCaseGid()],
        'method' => 'post',
        'options' => ['data-pjax' => true]
    ]); ?>
    <?php
    echo $form->errorSummary($model);
    ?>

    <?= $form->field($model, 'orderUid')->textInput(['maxlength' => 7]) ?>

    <div class="form-group text-center">
        <?= Html::submitButton('Add', ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
<?php Pjax::end(); ?>

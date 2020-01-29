<?php

use modules\offer\src\forms\OfferForm;
use modules\offer\src\entities\offer\OfferStatus;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model OfferForm */
/* @var $form yii\bootstrap4\ActiveForm */

$pjaxId = 'pjax-offer-form';
?>

<div class="offer-form">

    <script>
        pjaxOffFormSubmit('#<?=$pjaxId?>');
    </script>
    <?php \yii\widgets\Pjax::begin(['id' => $pjaxId, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
    <?php
    $form = ActiveForm::begin([
        'options' => ['data-pjax' => true],
        'action' => ['/offer/offer/update-ajax', 'id' => $model->of_id],
        'method' => 'post'
    ]);
    ?>

    <?php echo $form->errorSummary($model) ?>

    <?= $form->field($model, 'of_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'of_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'of_status_id')->dropDownList(OfferStatus::getList(), ['prompt' => '---']) ?>

    <?= $form->field($model, 'of_client_currency')->dropDownList(\common\models\Currency::getList(), ['prompt' => '---']) ?>

    <?= $form->field($model, 'of_client_currency_rate')->input('number', ['min' => 0, 'max' => 100, 'step' => 0.00001]) ?>

    <?= $form->field($model, 'of_app_total')->input('number', ['min' => 0, 'max' => 100000, 'step' => 0.01]) ?>

    <?= $form->field($model, 'of_client_total')->input('number', ['min' => 0, 'max' => 100000, 'step' => 0.01]) ?>

    <div class="form-group text-center">
        <?= Html::submitButton('<i class="fa fa-save"></i> Save offer', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php \yii\widgets\Pjax::end(); ?>

</div>
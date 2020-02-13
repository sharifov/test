<?php

use modules\offer\src\forms\OfferForm;
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
            'action' => ['/offer/offer/create-ajax', 'id' => $model->of_lead_id],
            'method' => 'post'
        ]);
        ?>

        <?php echo $form->errorSummary($model) ?>

        <?= $form->field($model, 'of_lead_id')->hiddenInput()->label(false) ?>

        <?= $form->field($model, 'of_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'of_client_currency')->dropDownList(\common\models\Currency::getList(), ['prompt' => '---']) ?>

        <div class="form-group text-center">
            <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    <?php \yii\widgets\Pjax::end(); ?>

</div>

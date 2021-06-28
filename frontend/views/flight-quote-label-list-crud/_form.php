<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\flightQuoteLabelList\entity\FlightQuoteLabelList */
/* @var $form ActiveForm */
?>

<div class="flight-quote-label-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'fqll_label_key')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'fqll_origin_description')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'fqll_description')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

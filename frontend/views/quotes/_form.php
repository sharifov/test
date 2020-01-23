<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Quote */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="quote-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'uid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lead_id')->textInput() ?>

    <?= $form->field($model, 'employee_id')->textInput() ?>

    <?= $form->field($model, 'record_locator')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pcc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cabin')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'gds')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'trip_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'main_airline_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'reservation_dump')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->dropDownList(\common\models\Quote::STATUS_LIST) ?>

    <?= $form->field($model, 'check_payment')->checkbox() ?>

    <?= $form->field($model, 'type_id')->dropDownList(\common\models\Quote::TYPE_LIST) ?>

    <?= $form->field($model, 'fare_type')->textInput(['maxlength' => true]) ?>

    <?//= $form->field($model, 'created')->textInput() ?>

    <?//= $form->field($model, 'updated')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

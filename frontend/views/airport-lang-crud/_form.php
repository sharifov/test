<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\airportLang\entity\AirportLang */
/* @var $form ActiveForm */
?>

<div class="airport-lang-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ail_iata')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ail_lang')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ail_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ail_city')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ail_country')->textInput(['maxlength' => true]) ?>


        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

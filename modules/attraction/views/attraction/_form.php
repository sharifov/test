<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\Hotel */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="attraction-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'atn_product_id')->textInput() ?>

            <?= $form->field($model, 'atn_date_from')->textInput() ?>

            <?= $form->field($model, 'atn_date_to')->textInput() ?>

            <?= $form->field($model, 'atn_destination')->textInput() ?>

            <?= $form->field($model, 'atn_destination_code')->textInput() ?>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

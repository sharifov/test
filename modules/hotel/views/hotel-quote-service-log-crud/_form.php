<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\HotelQuoteServiceLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hotel-quote-service-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'hqsl_hotel_quote_id')->textInput() ?>
    <?= $form->field($model, 'hqsl_action_type_id')->textInput() ?>
    <?= $form->field($model, 'hqsl_status_id')->textInput() ?>
    <?= $form->field($model, 'hqsl_message')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'hqsl_created_user_id')->textInput() ?>
    <?= $form->field($model, 'hqsl_created_dt')->textInput() ?>
    <?= $form->field($model, 'hqsl_updated_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

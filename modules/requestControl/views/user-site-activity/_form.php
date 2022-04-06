<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\requestControl\models\UserSiteActivity */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-site-activity-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'usa_user_id')->textInput() ?>

    <?= $form->field($model, 'usa_request_url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'usa_page_url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'usa_ip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'usa_request_type')->textInput() ?>

    <?= $form->field($model, 'usa_request_get')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'usa_request_post')->textarea(['rows' => 6]) ?>

    <?php //= $form->field($model, 'usa_created_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

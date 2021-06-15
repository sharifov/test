<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\visitorSubscription\entity\VisitorSubscription */
/* @var $form ActiveForm */
?>

<div class="visitor-subscription-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'vs_subscription_uid')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'vs_type_id')->textInput() ?>

        <?= $form->field($model, 'vs_enabled')->textInput() ?>

        <?= $form->field($model, 'vs_expired_date')->textInput() ?>

        <?= $form->field($model, 'vs_created_dt')->textInput() ?>

        <?= $form->field($model, 'vs_updated_dt')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

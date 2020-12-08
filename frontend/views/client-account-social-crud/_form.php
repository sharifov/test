<?php

use sales\model\clientAccountSocial\entity\ClientAccountSocial;
use sales\widgets\DateTimePicker;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var yii\web\View $this */
/* @var sales\model\clientAccountSocial\entity\ClientAccountSocial $model */
/* @var ActiveForm $form */
?>

<div class="client-account-social-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'cas_ca_id')->textInput() ?>

        <?= $form->field($model, 'cas_type_id')->dropDownList(ClientAccountSocial::TYPE_LIST, ['prompt' => '-']) ?>

        <?= $form->field($model, 'cas_identity')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cas_created_dt')->widget(DateTimePicker::class) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

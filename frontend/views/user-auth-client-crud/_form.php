<?php

use sales\model\userAuthClient\entity\UserAuthClientSources;
use sales\widgets\UserSelect2Widget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\userAuthClient\entity\UserAuthClient */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="auth-client-form">

    <div class="col-md-3">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'uac_user_id')->widget(UserSelect2Widget::class) ?>

        <?= $form->field($model, 'uac_source')->dropDownList(UserAuthClientSources::getList(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'uac_source_id')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'uac_email')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'uac_ip')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'uac_useragent')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>

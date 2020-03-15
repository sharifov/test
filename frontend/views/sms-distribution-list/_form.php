<?php

use sales\model\sms\entity\smsDistributionList\SmsDistributionList;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\sms\entity\smsDistributionList\SmsDistributionList */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sms-distribution-list-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-6">

        <?= $form->field($model, 'sdl_project_id')->dropDownList(\common\models\Project::getList(), ['prompt' => '-']) ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'sdl_phone_from')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'sdl_phone_to')->textInput(['maxlength' => true]) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <?= $form->field($model, 'sdl_status_id')->dropDownList(SmsDistributionList::getStatusList(), ['prompt' => '-']) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'sdl_priority')->textInput() ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'sdl_client_id')->input('number') ?>
            </div>
        </div>

        <?= $form->field($model, 'sdl_text')->textarea(['rows' => 6]) ?>

        <div class="row">
            <div class="col-md-6">
            <?= $form->field($model, 'sdl_start_dt')->textInput() ?>
            </div>
            <div class="col-md-6">
            <?= $form->field($model, 'sdl_end_dt')->textInput() ?>
            </div>
        </div>





        <?php //= $form->field($model, 'sdl_error_message')->textarea(['rows' => 6]) ?>

        <?php //= $form->field($model, 'sdl_message_sid')->textInput(['maxlength' => true]) ?>

        <?php //= $form->field($model, 'sdl_com_id')->textInput() ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'sdl_num_segments')->input('number') ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'sdl_price')->input('number', ['step' => 0.01]) ?>
            </div>
        </div>





        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

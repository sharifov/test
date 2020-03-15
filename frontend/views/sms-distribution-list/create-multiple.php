<?php

use sales\model\sms\entity\smsDistributionList\SmsDistributionList;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\sms\entity\smsDistributionList\forms\SmsDistributionListAddMultipleForm */

$this->title = 'Create Multiple Sms Distribution List';
$this->params['breadcrumbs'][] = ['label' => 'Sms Distribution List', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sms-distribution-list-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="sms-distribution-list-form">

        <?php $form = ActiveForm::begin(); ?>

        <div class="col-md-6">

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'sdl_project_id')->dropDownList(\common\models\Project::getList(), ['prompt' => '-']) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'sdl_status_id')->dropDownList(SmsDistributionList::getStatusList(), ['prompt' => '-']) ?>
                </div>
            </div>


            <?= $form->field($model, 'sdl_text')->textarea(['rows' => 6]) ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'sdl_phone_from')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'sdl_priority')->textInput() ?>
                </div>
            </div>



            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'sdl_start_dt')->input('datetime') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'sdl_end_dt')->input('datetime') ?>
                </div>
            </div>


            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'sdl_phone_to_list')->textarea(['rows' => 16]) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

<?php

use common\models\Call;
use common\models\Project;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\callTerminateLog\entity\CallTerminateLog */
/* @var $form ActiveForm */
?>

<div class="call-terminate-log-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ctl_call_phone_number')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ctl_call_status_id')->dropDownList(Call::STATUS_LIST) ?>

        <?= $form->field($model, 'ctl_project_id')->dropDownList(Project::getList()) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

<?php

use common\models\CallUserAccess;
use sales\widgets\DateTimePicker;
use sales\widgets\UserSelect2Widget;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\callLog\entity\callLogUserAccess\CallLogUserAccess */
/* @var $form ActiveForm */
?>

<div class="call-log-user-access-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'clua_cl_id')->textInput() ?>

        <?= $form->field($model, 'clua_user_id')->widget(UserSelect2Widget::class, [
            'data' => $model->clua_user_id ? [
                $model->clua_user_id => $model->user->username
            ] : [],
        ]) ?>

        <?= $form->field($model, 'clua_access_status_id')->dropDownList(CallUserAccess::getStatusTypeList(), ['prompt' => 'Select status']) ?>

        <?= $form->field($model, 'clua_access_start_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'clua_access_finish_dt')->widget(DateTimePicker::class) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>

<?php

use borales\extensions\phoneInput\PhoneInput;
use common\models\Call;
use common\models\Department;
use src\access\ListsAccess;
use src\auth\Auth;
use src\model\callLog\entity\callLog\CallLogCategory;
use src\model\callLog\entity\callLog\CallLogStatus;
use src\model\callLog\entity\callLog\CallLogType;
use src\widgets\DateTimePicker;
use src\widgets\PhoneSelect2Widget;
use src\widgets\UserSelect2Widget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\callLog\entity\callLog\CallLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="row">
    <div class="col-md-4">
        <div class="call-log-form">

            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'cl_group_id')->textInput() ?>

            <?= $form->field($model, 'cl_call_sid')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'cl_type_id')->dropDownList(CallLogType::getList(), ['prompt' => 'Select type']) ?>

            <?= $form->field($model, 'cl_category_id')->dropDownList(Call::SOURCE_LIST, ['prompt' => 'Select type']) ?>

            <?= $form->field($model, 'cl_is_transfer')->dropDownList([0 => 'No', 1 => 'Yes'], ['prompt' => 'Select is transfer']) ?>

            <?= $form->field($model, 'cl_duration')->textInput() ?>

            <?= $form->field($model, 'cl_phone_from')->widget(PhoneInput::class, [
                'jsOptions' => [
                    'nationalMode' => false,
                    'preferredCountries' => ['us'],
                    'customContainer' => 'intl-tel-input'
                ]
            ]) ?>

            <?= $form->field($model, 'cl_phone_to')->widget(PhoneInput::class, [
                'jsOptions' => [
                    'nationalMode' => false,
                    'preferredCountries' => ['us'],
                    'customContainer' => 'intl-tel-input'
                ]
            ]) ?>

            <?= $form->field($model, 'cl_phone_list_id')->widget(PhoneSelect2Widget::class, [
                'data' => $model->cl_phone_list_id ? [
                    $model->cl_phone_list_id => $model->phoneList->pl_phone_number
                ] : [],
            ]) ?>

            <?= $form->field($model, 'cl_user_id')->widget(UserSelect2Widget::class, [
                'data' => $model->cl_user_id ? [
                    $model->cl_user_id => $model->user->username
                ] : [],
            ]) ?>

            <?= $form->field($model, 'cl_department_id')->dropDownList(Department::getList(), ['prompt' => 'Select department']) ?>

            <?= $form->field($model, 'cl_project_id')->dropDownList((new ListsAccess(Auth::id()))->getProjects(), ['prompt' => 'Select project']) ?>

            <?= $form->field($model, 'cl_call_created_dt')->widget(DateTimePicker::class) ?>

            <?= $form->field($model, 'cl_call_finished_dt')->widget(DateTimePicker::class) ?>

            <?= $form->field($model, 'cl_status_id')->dropDownList(CallLogStatus::getList(), ['prompt' => 'Select status']) ?>

            <?= $form->field($model, 'cl_client_id')->textInput() ?>

            <?= $form->field($model, 'cl_price')->input('number', ['min' => 0, 'max' => 999999, 'step' => 0.00001]) ?>

            <?= $form->field($model, 'cl_stir_status')->dropDownList(Call::STIR_STATUS_LIST, ['prompt' => '---']) ?>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>

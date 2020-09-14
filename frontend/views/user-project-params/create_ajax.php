<?php

use common\models\UserProjectParams;
use sales\access\EmployeeProjectAccess;
use sales\widgets\EmailSelect2Widget;
use sales\widgets\PhoneSelect2Widget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use borales\extensions\phoneInput\PhoneInput;

/* @var $this yii\web\View */
/* @var $model common\models\UserProjectParams */
/* @var $form yii\widgets\ActiveForm */


?>

    <?php
        $projectList = EmployeeProjectAccess::getProjects(Yii::$app->user->id);
    ?>

<script>
    pjaxOffFormSubmit('#create-app-pjax');
</script>

<?php \yii\widgets\Pjax::begin(['id' => 'create-app-pjax', 'timeout' => 2000, 'enablePushState' => false]); ?>
<?php $form = ActiveForm::begin(['options' => ['data-pjax' => true], 'action' => ['user-project-params/create-ajax', 'user_id' => $model->upp_user_id], 'method' => 'post', 'enableClientValidation' => false]); ?>

<div class="col-md-12">
    <?php //= $form->field($model, 'upp_user_id')->dropDownList($userList, ['prompt' => '-']) ?>
    <?php
        echo $form->field($model, 'upp_user_id')->hiddenInput()->label(false);
    ?>

    <div class="form-group">
        <label class="control-label">Username</label>
        <?=Html::input('text', 'username', $model->uppUser->username, ['class' => 'form-control', 'readonly' => true, 'disabled' => true]); ?>
    </div>

    <?= $form->field($model, 'upp_project_id')->dropDownList($projectList, [
        'prompt' => '-',
        'id' => 'project_id',
    ]) ?>

    <?= $form->field($model, 'upp_dep_id')->dropDownList(\common\models\Department::getList(), ['prompt' => '-']) ?>

    <?php //= $form->field($model, 'upp_email')->input('email', ['maxlength' => true]) ?>

    <?= $form->field($model, 'upp_email_list_id')->widget(EmailSelect2Widget::class, [
        'data' => $model->upp_email_list_id ? [
            $model->upp_email_list_id => $model->emailList->el_email
        ] : [],
        'withProject' => true,
    ]) ?>

    <?php //= $form->field($model, 'upp_tw_phone_number')->textInput(['maxlength' => true]) ?>


    <?php /* = $form->field($model, 'upp_tw_phone_number')->widget(PhoneInput::class, [
        'jsOptions' => [
            'autoPlaceholder' => 'off',
            'customPlaceholder' => '',
            'allowDropdown' => false,
            'preferredCountries' => ['us'],
            'customContainer' => 'intl-tel-input'
        ]
    ]) */ ?>

    <?= $form->field($model, 'upp_phone_list_id')->widget(PhoneSelect2Widget::class, [
        'data' => $model->upp_phone_list_id ? [
            $model->upp_phone_list_id => $model->phoneList->pl_phone_number
        ] : [],
    ]) ?>

    <?= $form->field($model, 'upp_allow_general_line')->checkbox() ?>

    <?= $form->field($model, 'upp_vm_enabled')->checkbox() ?>
    <?= $form->field($model, 'upp_vm_user_status_id')->dropDownList(UserProjectParams::VM_USER_STATUS_LIST, ['prompt' => 'Select user status']) ?>
    <?= $form->field($model, 'upp_vm_id')->dropDownList($model->getAvailableVoiceMail(), ['prompt' => 'Select voice mail']) ?>

    <?php //= $form->field($model, 'upp_tw_sip_id')->textInput(['maxlength' => true]) ?>

    <?php //= Html::input('hidden', 'redirect', Yii::$app->request->get('redirect')) ?>

    <div class="form-group text-center">
        <?= Html::submitButton('Save Params', ['class' => 'btn btn-success']) ?>
    </div>
</div>


<?php ActiveForm::end(); ?>
<?php \yii\widgets\Pjax::end(); ?>

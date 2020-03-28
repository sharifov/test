<?php

use common\models\Employee;
use kartik\select2\Select2;
use sales\access\EmployeeProjectAccess;
use sales\widgets\EmailSelect2Widget;
use sales\widgets\PhoneSelect2Widget;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveForm;
use borales\extensions\phoneInput\PhoneInput;
//use frontend\extensions\PhoneInput;

/* @var $this yii\web\View */
/* @var $model common\models\UserProjectParams */
/* @var $form yii\widgets\ActiveForm */

/** @var Employee $user */
$user = Yii::$app->user->identity;

$userList = [];

if ($user->isAdmin() || $user->isUserManager()) {
    $userList = \common\models\Employee::getList();
} else {
    $userList = \common\models\Employee::getListByUserId($user->id);
}

$projectList = EmployeeProjectAccess::getProjects($user->id);

?>

<div class="user-project-params-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-2">

        <?= $form->field($model, 'upp_user_id')->dropDownList($userList, ['prompt' => '-']) ?>

        <?= $form->field($model, 'upp_project_id')->dropDownList($projectList, ['prompt' => '-']) ?>

        <?= $form->field($model, 'upp_dep_id')->dropDownList(\common\models\Department::getList(), ['prompt' => '-']) ?>

        <?php //= $form->field($model, 'upp_email')->input('email', ['maxlength' => true]) ?>

        <?= $form->field($model, 'upp_email_list_id')->widget(EmailSelect2Widget::class, [
            'data' => $model->upp_email_list_id ? [
                $model->upp_email_list_id => $model->emailList->el_email
            ] : [],
        ]) ?>

        <?= $form->field($model, 'upp_tw_phone_number')->widget(PhoneInput::class, [
            'jsOptions' => [
                'formatOnDisplay' => false,
                'autoPlaceholder' => 'off',
                'customPlaceholder' => '',
                'allowDropdown' => false,
                'preferredCountries' => ['us'],
                'customContainer' => 'intl-tel-input'
            ]
        ]) ?>

        <?= $form->field($model, 'upp_phone_list_id')->widget(PhoneSelect2Widget::class, [
                'data' => $model->upp_phone_list_id ? [
                        $model->upp_phone_list_id => $model->phoneList->pl_phone_number
                ] : [],
        ]) ?>

        <?= $form->field($model, 'upp_allow_general_line')->checkbox() ?>

        <?php // = $form->field($model, 'upp_tw_sip_id')->textInput(['maxlength' => true]) ?>


        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use sales\access\EmployeeProjectAccess;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use borales\extensions\phoneInput\PhoneInput;
//use frontend\extensions\PhoneInput;

/* @var $this yii\web\View */
/* @var $model common\models\UserProjectParams */
/* @var $form yii\widgets\ActiveForm */

$userList = [];

if (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) || Yii::$app->authManager->getAssignment('userManager', Yii::$app->user->id)) {
    $userList = \common\models\Employee::getList();
} else {
    $userList = \common\models\Employee::getListByUserId(Yii::$app->user->id);
}

$projectList = EmployeeProjectAccess::getProjects(Yii::$app->user->id);

//Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) ? \common\models\UserGroup::getList() : Yii::$app->user->identity->getUserGroupList()


?>

<div class="user-project-params-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-2">
        <?= $form->field($model, 'upp_user_id')->dropDownList($userList, ['prompt' => '-']) ?>

        <?= $form->field($model, 'upp_project_id')->dropDownList($projectList, ['prompt' => '-']) ?>

        <?= $form->field($model, 'upp_dep_id')->dropDownList(\common\models\Department::getList(), ['prompt' => '-']) ?>

        <?= $form->field($model, 'upp_email')->input('email', ['maxlength' => true]) ?>



        <?= $form->field($model, 'upp_tw_phone_number')->widget(PhoneInput::class, [
            'jsOptions' => [
                'formatOnDisplay' => false,
                'autoPlaceholder' => 'off',
                'customPlaceholder' => '',
                'allowDropdown' => false,
                'preferredCountries' => ['us'],
            ]
        ]) ?>

        <?= $form->field($model, 'upp_allow_general_line')->checkbox() ?>

        <?// = $form->field($model, 'upp_tw_sip_id')->textInput(['maxlength' => true]) ?>


        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserProjectParams */
/* @var $form yii\widgets\ActiveForm */

$userList = [];
$projectList = [];

if (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) || Yii::$app->authManager->getAssignment('userManager', Yii::$app->user->id)) {
    $userList = \common\models\Employee::getList();
    $projectList = \common\models\Project::getList();
} else {
    $userList = \common\models\Employee::getListByUserId(Yii::$app->user->id);
    $projectList = \common\models\ProjectEmployeeAccess::getProjectsByEmployee();
}

//Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) ? \common\models\UserGroup::getList() : Yii::$app->user->identity->getUserGroupList()


?>

<div class="user-project-params-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-2">
        <?= $form->field($model, 'upp_user_id')->dropDownList($userList, ['prompt' => '-']) ?>

        <?= $form->field($model, 'upp_project_id')->dropDownList($projectList, ['prompt' => '-']) ?>

        <?= $form->field($model, 'upp_email')->input('email', ['maxlength' => true]) ?>

        <?//= $form->field($model, 'upp_phone_number')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'upp_phone_number')->widget(\borales\extensions\phoneInput\PhoneInput::class, [
            'jsOptions' => [
                'allowExtensions' => true,
                'preferredCountries' => ['us'],
            ]
        ]) ?>

        <?= $form->field($model, 'upp_tw_phone_number')->widget(\borales\extensions\phoneInput\PhoneInput::class, [
            'jsOptions' => [
                'preferredCountries' => ['us'],
            ]
        ]) ?>

        <?//= $form->field($model, 'upp_tw_phone_number')->textInput(['maxlength' => true]) ?>

        <?// = $form->field($model, 'upp_tw_sip_id')->textInput(['maxlength' => true]) ?>


        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

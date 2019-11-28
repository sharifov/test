<?php

use sales\access\EmployeeProjectAccess;
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

<?php \yii\widgets\Pjax::begin(['id' => 'create-app-pjax', 'timeout' => 2000, 'enablePushState' => false]); ?>
<?php $form = ActiveForm::begin(['options' => ['data-pjax' => true], 'action' => ['user-project-params/create-ajax'], 'method' => 'post']); ?>

<div class="col-md-12">
    <?//= $form->field($model, 'upp_user_id')->dropDownList($userList, ['prompt' => '-']) ?>
    <?php
        echo $form->field($model, 'upp_user_id')->hiddenInput()->label(false);
    ?>

    <div class="form-group">
        <label class="control-label">Username</label>
        <?=Html::input('text', 'username', $model->uppUser->username, ['class' => 'form-control', 'readonly' => true, 'disabled' => true]); ?>
    </div>

    <?= $form->field($model, 'upp_project_id')->dropDownList($projectList, ['prompt' => '-']) ?>

    <?= $form->field($model, 'upp_dep_id')->dropDownList(\common\models\Department::getList(), ['prompt' => '-']) ?>

    <?= $form->field($model, 'upp_email')->input('email', ['maxlength' => true]) ?>


    <?//= $form->field($model, 'upp_tw_phone_number')->textInput(['maxlength' => true]) ?>


    <?= $form->field($model, 'upp_tw_phone_number')->widget(PhoneInput::class, [
        'jsOptions' => [
            'autoPlaceholder' => 'off',
            'customPlaceholder' => '',
            'allowDropdown' => false,
            'preferredCountries' => ['us'],
        ]
    ]) ?>



    <?= $form->field($model, 'upp_allow_general_line')->checkbox() ?>

    <?php //= $form->field($model, 'upp_tw_sip_id')->textInput(['maxlength' => true]) ?>

    <?//= Html::input('hidden', 'redirect', Yii::$app->request->get('redirect')) ?>

    <div class="form-group text-center">
        <?= Html::submitButton('Save Params', ['class' => 'btn btn-success']) ?>
    </div>
</div>


<?php ActiveForm::end(); ?>
<?php \yii\widgets\Pjax::end(); ?>

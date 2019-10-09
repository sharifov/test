<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
//use borales\extensions\phoneInput\PhoneInput;
use frontend\extensions\PhoneInput;

/* @var $this yii\web\View */
/* @var $model common\models\UserProjectParams */
/* @var $form yii\bootstrap\ActiveForm */

$this->title = 'Update Project Params';
//$this->params['breadcrumbs'][] = ['label' => 'User Project Params', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-project-params-create">


    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><?= Html::encode($this->title) ?></h4>
    </div>


    <div class="modal-body">
        <?php

            /*$userList = [];
            $projectList = [];

            if (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)) {
                $userList = \common\models\Employee::getList();
                $projectList = \common\models\Project::getList();
            } else {
                $userList = \common\models\Employee::getListByUserId(Yii::$app->user->id);
                $projectList = \common\models\ProjectEmployeeAccess::getProjectsByEmployee();
            }*/

            //Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) ? \common\models\UserGroup::getList() : Yii::$app->user->identity->getUserGroupList()


        ?>

        <div class="user-project-params-form">

            <?php \yii\widgets\Pjax::begin(['id' => 'update-app-pjax', 'timeout' => 2000, 'enablePushState' => false]); ?>
            <?php $form = ActiveForm::begin(['options' => ['data-pjax' => true], 'action' => ['user-project-params/update-ajax', 'data[upp_user_id]' => $model->upp_user_id, 'data[upp_project_id]' => $model->upp_project_id], 'method' => 'POST']); ?>


            <div class="col-md-12">
                <?//= $form->field($model, 'upp_user_id')->dropDownList($userList, ['prompt' => '-']) ?>
                <?php
                    //echo $form->field($model, 'upp_user_id')->hiddenInput(['prompt' => '-'])->label(false);
                ?>

                <div class="form-group">
                    <label class="control-label">Username</label>
                    <?=Html::input('text', 'username', $model->uppUser->username, ['class' => 'form-control', 'readonly' => true, 'disabled' => true]); ?>
                </div>

                <div class="form-group">
                    <label class="control-label">Project</label>
                    <?=Html::input('text', 'project', $model->uppProject->name, ['class' => 'form-control', 'readonly' => true, 'disabled' => true]); ?>
                </div>


                <?= $form->field($model, 'upp_dep_id')->dropDownList(\common\models\Department::getList(), ['prompt' => '-']) ?>


                <?//= $form->field($model, 'upp_project_id')->dropDownList($projectList, ['disabled' => true]) ?>

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

                <?//= $form->field($model, 'upp_tw_phone_number')->textInput(['maxlength' => true]) ?>

                <?//= $form->field($model, 'upp_tw_sip_id')->textInput(['maxlength' => true]) ?>

                <?= Html::input('hidden', 'redirect', Yii::$app->request->get('redirect')) ?>

                <div class="form-group text-center">
                    <?= Html::submitButton('Save Params', ['class' => 'btn btn-success']) ?>
                </div>
            </div>


            <?php ActiveForm::end(); ?>
            <?php \yii\widgets\Pjax::end(); ?>

        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <?/*= Html::submitButton('Save', ['class' => 'btn btn-success'])*/ ?>
        <?php /*<button type="button" class="btn btn-primary">Save changes</button>*/ ?>
    </div>


</div>

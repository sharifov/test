<?php

use common\models\Client;
use common\models\UserContactList;
use sales\access\EmployeeProjectAccess;
use sales\auth\Auth;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Client */
/* @var $form yii\widgets\ActiveForm */

$projectList = EmployeeProjectAccess::getProjects(Yii::$app->user->id);

?>

<div class="client-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php /* echo $form->field($model, 'uuid')->textInput(['maxlength' => true]) */?>

    <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'middle_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'company_name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'description')->textarea() ?>
    <?= $form->field($model, 'is_company')->checkbox() ?>
    <?= $form->field($model, 'is_public')->checkbox() ?>
    <?= $form->field($model, 'disabled')->checkbox() ?>
    <?= $form->field($model, 'rating')->textInput(['type' => 'number', 'step' => 1]) ?>
    <?= $form->field($model, 'parent_id')->textInput() ?>
    <?= $form->field($model, 'cl_project_id')->dropDownList($projectList, ['prompt' => '-']) ?>
    <?= $form->field($model, 'cl_type_id')->dropDownList($model::TYPE_LIST) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ApiUser */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="api-user-form">

    <div class="panel panel-default">

    <div class="panel-body panel-collapse collapse in">
        <?php $form = ActiveForm::begin(); ?>

        <div class="col-md-4">

            <?= $form->field($model, 'au_name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'au_api_username')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'au_api_password')->passwordInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'au_email')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'au_project_id')->dropDownList(\yii\helpers\ArrayHelper::map(\common\models\Project::find()->all(), 'id', 'name'), ['prompt' => '---']) ?>

            <?= $form->field($model, 'au_enabled')->checkbox() ?>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
    </div>

</div>

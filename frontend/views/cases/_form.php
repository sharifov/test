<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\entities\cases\Cases */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cases-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-4">
        <?= $form->field($model, 'cs_project_id')->dropDownList(\common\models\Project::getList()) ?>

        <?= $form->field($model, 'cs_subject')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cs_description')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'cs_category_id')->dropDownList(\yii\helpers\ArrayHelper::map(\src\entities\cases\CaseCategory::find()->asArray()->all(), 'cc_id', 'cc_name'), ['prompt' => '-']) ?>

        <?= $form->field($model, 'cs_client_id')->textInput() ?>

        <?= $form->field($model, 'cs_dep_id')->dropDownList(\common\models\Department::getList(), ['prompt' => '-']) ?>

        <?php //= $form->field($model, 'cs_client_id')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

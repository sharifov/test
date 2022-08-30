<?php

use src\entities\cases\CaseCategory;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Department;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model src\entities\cases\CaseCategory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="case-category-form">
    <div class="x_panel">
        <div class="x_title">
            <h2><i class="fa fa-cube"></i> Create New Category</h2>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: block;">
            <?php
            $form = ActiveForm::begin(); ?>
            <div class="col-md-4">

                <?php
                echo $form->errorSummary($model);
                ?>
                <?= $form->field($model, 'cc_key')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'cc_name')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'cc_dep_id')->dropDownList(Department::getList(), [
                    'prompt' => 'Select department',
                ]) ?>

                <?php echo $form->field($model, 'parentCategoryId')
                    ->dropDownList(\yii\helpers\ArrayHelper::map(CaseCategory::findNestedSets()->all(), 'cc_id', 'cc_name'), ['prompt' => 'Choose a parent category']); ?>

                <?= $form->field($model, 'cc_allow_to_select')->checkbox() ?>
                <?= $form->field($model, 'cc_system')->checkbox() ?>
                <?= $form->field($model, 'cc_enabled')->checkbox() ?>

                <div class="form-group">
                    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                </div>
            </div>
            <?php
            ActiveForm::end(); ?>

        </div>
    </div>
</div>
<?php

use src\entities\cases\CaseCategory;
use src\forms\cases\CaseCategoryManageForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Department;

/* @var $this yii\web\View */
/* @var $model src\forms\cases\CaseCategoryManageForm; */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="case-category-form">
    <div class="x_panel">
        <div class="x_content" style="display: block;">
            <?php
            $form = ActiveForm::begin(); ?>
            <div class="col-md-4">

                <?= $form->errorSummary($model); ?>
                <?= $form->field($model, 'cc_key')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'cc_name')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'cc_dep_id')->dropDownList(Department::getList(), [
                  'prompt' => 'Select department',
                ]) ?>

                <?php
                $caseCategoriesList = ArrayHelper::map(CaseCategory::findNestedSets()->all(), 'cc_id', 'cc_name');

                //delete from list current id to prevent setting model as self parent
                if ($model->scenario === CaseCategoryManageForm::SCENARIO_UPDATE) {
                    $currentModelId = $model->cc_id;
                    if (!$model->parentCategoryId) {
                        unset($caseCategoriesList[$currentModelId]);
                    }
                }
                echo $form->field($model, 'parentCategoryId')->dropDownList($caseCategoriesList, ['prompt' => 'Choose a parent category']);
                ?>

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

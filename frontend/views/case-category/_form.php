<?php

use kartik\tree\TreeViewInput;
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

                <div class="form-group">
                    <?php
                    echo Html::label('Parent Category');
                    $condition = [];
                    if ($model->scenario === CaseCategoryManageForm::SCENARIO_UPDATE) {
                        $currentModelId = $model->cc_id;
                        $condition = ['<>', 'cc_id', $currentModelId];
                    }
                    echo TreeViewInput::widget([
                      'name' => 'kvTreeInput',
                      'query' => CaseCategory::findNestedSets()->addOrderBy('cc_tree, cc_lft')->andWhere($condition),
                      'headingOptions' => ['label' => 'Case Categories'],
                      'rootOptions' => ['label' => '<i class="fas fa-tree text-success"></i>'],
                      'fontAwesome' => true,
                      'model' => $model,
                      'attribute' => 'parentCategoryId',
                      'autoCloseOnSelect' => true,
                      'asDropdown' => true,
                      'multiple' => false,
                      'options' => ['disabled' => false]
                    ]);

                    ?>
                </div>


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

<?php

use frontend\widgets\nestedSets\NestedSetsWidget;
use src\entities\cases\CaseCategory;
use src\forms\cases\CaseCategoryManageForm;
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
                $params = [
                    'query' => CaseCategory::findNestedSets(),
                    'attribute' => 'parentCategoryId',
                    'model' => $model
                ];
                if ($model->scenario === CaseCategoryManageForm::SCENARIO_UPDATE) {
                    $currentModelId = $model->cc_id;
                    $params['currentModelId'] = $currentModelId;
                    if (isset($model->parentCategoryId)) {
                        $params['parentCategoryId'] = $model->parentCategoryId;
                    }
                }
                echo NestedSetsWidget::widget($params);
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

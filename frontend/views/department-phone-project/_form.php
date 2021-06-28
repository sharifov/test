<?php

use sales\widgets\PhoneSelect2Widget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\DepartmentPhoneProject */
/* @var $form yii\widgets\ActiveForm */
?>

<style>
    .jsoneditor-mode-code {
        height: 800px;
    }
</style>

<div class="department-phone-project-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-4">




        <?php //= $form->field($model, 'dpp_phone_number')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'dpp_phone_list_id')->widget(PhoneSelect2Widget::class, [
            'data' => $model->dpp_phone_list_id ? [
                $model->dpp_phone_list_id => $model->phoneList->pl_phone_number
            ] : [],
        ]) ?>
        <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'dpp_project_id')->dropDownList(\common\models\Project::getList(), ['prompt' => '-']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'dpp_dep_id')->dropDownList(\common\models\Department::getList(), ['prompt' => '-']) ?>
        </div>
        </div>

        <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'dpp_redial')->dropDownList([0 => 'No', 1 => 'Yes']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'dpp_language_id')->dropDownList(\common\models\Language::getLanguages(true, 'language'), ['prompt' => '---']) ?>
        </div>
        </div>

        <?php //= $form->field($model, 'dpp_source_id')->dropDownList(Sour) ?>

        <?php
            echo $form->field($model, 'dpp_source_id')->widget(\kartik\select2\Select2::class, [
                'data' => \common\models\Sources::getList(true),
                'size' => \kartik\select2\Select2::SMALL,
                'options' => ['placeholder' => 'Select market', 'multiple' => false],
                'pluginOptions' => ['allowClear' => true],
            ]);
            ?>

        <?php
        echo $form->field($model, 'user_group_list')->widget(\kartik\select2\Select2::class, [
            'data' => \common\models\UserGroup::getList(),
            'size' => \kartik\select2\Select2::SMALL,
            'options' => ['placeholder' => 'Select User Groups', 'multiple' => true],
            'pluginOptions' => ['allowClear' => true],
        ]);
        ?>

        <?php //= $form->field($model, 'dpp_params')->textInput() ?>

        <?= $form->field($model, 'dpp_enable')->checkbox() ?>

        <?= $form->field($model, 'dpp_description')->textarea() ?>

        <?= $form->field($model, 'dpp_ivr_enable')->checkbox() ?>

        <?= $form->field($model, 'dpp_default')->checkbox() ?>

        <?= $form->field($model, 'dpp_allow_transfer')->checkbox() ?>

        <?= $form->field($model, 'dpp_show_on_site')->checkbox() ?>

        <?= $form->field($model, 'dpp_priority')->textInput() ?>



        <?php //= $form->field($model, 'dpp_updated_user_id')->textInput() ?>

        <?php //= $form->field($model, 'dpp_updated_dt')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <div class="col-md-6">
        <?php

        try {
            echo $form->field($model, 'dpp_params')->widget(
                \kdn\yii2\JsonEditor::class,
                [
                    'clientOptions' => [
                        'modes' => ['code', 'form', 'tree', 'view'], //'text',
                        'mode' => 'tree'
                    ],
                    //'collapseAll' => ['view'],
                    'expandAll' => ['tree', 'form'],
                ]
            );
        } catch (Exception $exception) {
            echo $form->field($model, 'dpp_params')->textarea(['rows' => 6]);
        }

        ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

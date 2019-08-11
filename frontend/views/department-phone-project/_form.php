<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\DepartmentPhoneProject */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="department-phone-project-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-4">
        <?= $form->field($model, 'dpp_dep_id')->dropDownList(\common\models\Department::getList(), ['prompt' => '-']) ?>

        <?= $form->field($model, 'dpp_project_id')->dropDownList(\common\models\Project::getList(), ['prompt' => '-']) ?>

        <?= $form->field($model, 'dpp_phone_number')->textInput(['maxlength' => true]) ?>

        <?//= $form->field($model, 'dpp_source_id')->dropDownList(Sour) ?>

        <?php
        echo $form->field($model, 'dpp_source_id')->widget(\kartik\select2\Select2::class, [
            'data' => \common\models\Sources::getList(true),
            'size' => \kartik\select2\Select2::SMALL,
            'options' => ['placeholder' => 'Select market', 'multiple' => false],
            'pluginOptions' => ['allowClear' => true],
        ]);
        ?>

        <?//= $form->field($model, 'dpp_params')->textInput() ?>

        <?= $form->field($model, 'dpp_enable')->checkbox() ?>

        <?= $form->field($model, 'dpp_avr_enable')->checkbox() ?>



        <?//= $form->field($model, 'dpp_updated_user_id')->textInput() ?>

        <?//= $form->field($model, 'dpp_updated_dt')->textInput() ?>

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

<?php

use sales\model\phoneLine\phoneLine\entity\PhoneLine;
use yii\bootstrap4\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model PhoneLine */
/* @var $form ActiveForm */
?>

<div class="phone-line-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'line_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'line_project_id')->widget(\sales\widgets\ProjectSelect2Widget::class, [
            'pluginOptions' => [
                'allowClear' => true,
                'templateSelection' => new JsExpression('function (data) { return data.text || data.selection;}'),
            ],
            'initValueText' => $model->line_project_id ? $model->lineProject->name : null
        ]) ?>

        <?= $form->field($model, 'line_dep_id')->widget(\sales\widgets\DepartmentSelect2Widget::class, [
            'pluginOptions' => [
                'allowClear' => true,
                'templateSelection' => new JsExpression('function (data) { return data.text || data.selection;}'),
            ],
            'initValueText' => $model->line_dep_id ? $model->lineDep->dep_name : null
        ]) ?>

        <?= $form->field($model, 'line_language_id')->widget(\kartik\select2\Select2::class, [
            'data' => \common\models\Language::getList(),
            'pluginOptions' => [
                'allowClear' => true
            ],
            'options' => [
                'prompt' => '---'
            ]
        ]) ?>

        <?= $form->field($model, 'line_personal_user_id')->widget(\sales\widgets\UserSelect2Widget::class) ?>

        <?= $form->field($model, 'line_uvm_id')->textInput() ?>

        <?= $form->field($model, 'line_allow_in')->checkbox() ?>

        <?= $form->field($model, 'line_allow_out')->checkbox() ?>

        <?= $form->field($model, 'line_enabled')->checkbox() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
    <div class="col-md-4">
        <?php

        try {
            echo $form->field($model, 'line_settings_json')->widget(
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
            echo $form->field($model, 'line_settings_json')->textarea(['rows' => 6]);
        }

        ?>
    </div>

</div>

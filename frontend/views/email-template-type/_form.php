<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Json;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\EmailTemplateType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="email-template-type-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-4">
    <?= $form->field($model, 'etp_key')->textInput(['maxlength' => true, 'disabled' => true]) ?>

    <?= $form->field($model, 'etp_origin_name')->textInput(['maxlength' => true, 'disabled' => true]) ?>

    <?= $form->field($model, 'etp_name')->textInput(['maxlength' => true]) ?>

    <?php // $form->field($model, 'etp_dep_id')->dropDownList(\common\models\Department::getList(), ['prompt' => '-']) ?>

        <?= $form->field($model, 'departmentIds')->widget(Select2::class, [
            'data' => \common\models\Department::getList(),
            'size' => Select2::SMALL,
            'options' => [
                'placeholder' => 'Select department',
                'multiple' => true,
            ],
            'pluginOptions' => ['allowClear' => true],
        ]);
?>
        <?= $form->field($model, 'projectIds')->widget(Select2::class, [
            'data' => \common\models\Project::getList(),
            'size' => Select2::SMALL,
            'options' => [
                'placeholder' => 'Select project',
                'multiple' => true,
            ],
            'pluginOptions' => ['allowClear' => true],
        ]);
?>

    <?= $form->field($model, 'etp_ignore_unsubscribe')->checkbox() ?>

    <?= $form->field($model, 'etp_hidden')->checkbox() ?>

        <?php

        try {
            $model->etp_params_json = Json::encode($model->etp_params_json);
            echo $form->field($model, 'etp_params_json')->widget(
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
            $model->etp_params_json = '{}';
            echo $form->field($model, 'etp_params_json')->textarea(['rows' => 6]);
        }
        ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

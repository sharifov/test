<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Project */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-6">
        <?= $form->field($model, 'project_key')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'link')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'api_key')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'email_postfix')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ga_tracking_id')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'sort_order')->input('number', ['min' => 0, 'max' => 100, 'step' => 1, 'class' => 'form-control w-25']) ?>

        <?php //= $form->field($model, 'contact_info')->textarea(['rows' => 6]) ?>

        <?php
        echo $form->field($model, 'relatedProjects')->widget(\kartik\select2\Select2::class, [
            'data' => \common\models\Project::getListExcludeIds([$model->id]),
            'size' => \kartik\select2\Select2::SMALL,
            'options' => [
                'placeholder' => 'Select projects',
                'multiple' => true,
                'value' => $model->relatedProjects ?? $model->getRelatedProjectIds(),
            ],
            'pluginOptions' => ['allowClear' => true],
        ]);
        ?>

        <?php

        try {
            echo $form->field($model, 'contact_info')->widget(
                \kdn\yii2\JsonEditor::class,
                [
                    'clientOptions' => [
                        'modes' => ['code', 'form', 'tree', 'view'], //'text',
                        'mode' => 'tree'
                    ],
                    //'collapseAll' => ['view'],
                    'expandAll' => ['tree', 'form'],
                    'value' => @json_encode(\yii\helpers\ArrayHelper::merge($model->contactInfo->attributes, json_decode($model->contact_info)))
                ]
            );
        } catch (Exception $exception) {
            echo $form->field($model, 'contact_info')->textarea(['rows' => 6]);
        }

        ?>


        <?= $form->field($model, 'closed')->checkbox() ?>

    </div>
    <div class="col-md-6">


    <?php /*= $form->field($model, 'last_update')->textInput()*/ ?>


    <?php

    try {
        $model->p_params_json = Json::encode($model->p_params_json);
        echo $form->field($model, 'p_params_json')->widget(
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
        $model->p_params_json = '{}';
        echo $form->field($model, 'p_params_json')->textarea(['rows' => 6]);
    }

    ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

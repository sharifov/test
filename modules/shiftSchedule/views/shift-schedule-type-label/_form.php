<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\shiftSchedule\src\entities\shiftScheduleTypeLabel\ShiftScheduleTypeLabel */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="shift-schedule-type-label-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4">

        <?= $form->field($model, 'stl_key')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'stl_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'stl_enabled')->checkbox() ?>



        </div>
        <div class="col-md-4">

            <?php

            try {
                echo $form->field($model, 'stl_params_json')->widget(
                    \kdn\yii2\JsonEditor::class,
                    [
                        'clientOptions' => [
                            'modes' => ['code', 'form', 'tree', 'view'], //'text',
                            'mode' => $model->isNewRecord ? 'code' : 'form'
                        ],
                        //'collapseAll' => ['view'],
                        'expandAll' => ['tree', 'form'],
                        'value' => $model->stl_params_json ? json_encode($model->stl_params_json) : ''
                    ]
                );
            } catch (Exception $exception) {
                echo $form->field($model, 'stl_params_json')
                    ->textarea(['rows' => 6, 'value' => json_encode($model->stl_params_json)]);
            }

            ?>

            <div class="col-md-4">
                <?= $form->field($model, 'stl_color')->input('color') ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($model, 'stl_icon_class')->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-md-4">
                <?= $form->field($model, 'stl_sort_order')->input('number', ['min' => 0]) ?>
            </div>


        </div>
    </div>

    <div class="row">
        <div class="col-md-12 text-left">
            <div class="form-group">
                <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

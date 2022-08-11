<?php

use kdn\yii2\JsonEditor;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\shiftSchedule\src\entities\userShiftScheduleLog\UserShiftScheduleLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-shift-schedule-log-form">
    <div class="row">
        <div class="col-md-5">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'ussl_uss_id')->textInput() ?>

            <?= $form->field($model, 'ussl_old_attr')->widget(
                JsonEditor::class,
                [
                    'clientOptions' => [
                        'modes' => ['code', 'form', 'tree', 'view'],
                        'mode' => 'tree'
                    ],
                    'expandAll' => ['tree', 'form'],
                ]
            ) ?>

            <?= $form->field($model, 'ussl_new_attr')->widget(
                JsonEditor::class,
                [
                    'clientOptions' => [
                        'modes' => ['code', 'form', 'tree', 'view'],
                        'mode' => 'tree'
                    ],
                    'expandAll' => ['tree', 'form'],
                ]
            ) ?>

            <?= $form->field($model, 'ussl_formatted_attr')->widget(
                JsonEditor::class,
                [
                    'clientOptions' => [
                        'modes' => ['code', 'form', 'tree', 'view'],
                        'mode' => 'tree'
                    ],
                    'expandAll' => ['tree', 'form'],
                ]
            ); ?>

            <?= $form->field($model, 'ussl_month_start')->input('number') ?>

            <?= $form->field($model, 'ussl_year_start')->input('number') ?>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

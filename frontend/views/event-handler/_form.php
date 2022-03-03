<?php

use frontend\helpers\JsonHelper;
use src\helpers\app\AppHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\eventManager\src\entities\EventHandler */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="event-handler-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'eh_el_id')->textInput() ?>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <?= $form->field($model, 'eh_class')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-lg-6">
                    <?= $form->field($model, 'eh_method')->textInput(['maxlength' => true]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'eh_enable_type')
                        ->dropDownList(\modules\eventManager\src\entities\EventList::getEnableTypeList()) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'eh_sort_order')->input(
                        'number',
                        ['min' => 0, 'max' => 1000, 'step' => 1]
                    ) ?>
                </div>
            </div>


            <?= $form->field($model, 'eh_enable_log')->checkbox() ?>

            <?= $form->field($model, 'eh_asynch')->checkbox() ?>

            <?= $form->field($model, 'eh_break')->checkbox() ?>

            <div class="row">

            </div>



            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'eh_cron_expression')->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">

                    Example:
                    <pre>
 1  2  3  4  5
 *  *  *  *  *
 │  │  │  │  └──────── day of week (0 - 6) (Sunday=0 or 7) OR sun,mon,tue,wed,thu,fri,sat
 │  │  │  └──────── month (1 - 12) OR jan,feb,mar,apr ...
 │  │  └──────── day of month (1 - 31)
 │  └──────── hour (0 - 23)
 └──────── minute (0 - 59)
                </pre>

                    <?= Html::a(
                            '<i class="fa fa-link"></i> Editor for schedule expressions',
                            'https://crontab.guru/#' . str_replace(' ', '_', $model->eh_cron_expression),
                            ['class' => 'btn btn-xs btn-warning', 'target' => '_blank']
                        ) ?>


                </div>
            </div>
        </div>
        <div class="col-md-6">
            <?php echo $form->field($model, 'eh_condition')->textarea(['rows' => 6]) ?>

            <?php
            $model->eh_params = JsonHelper::encode($model->eh_params);
            try {
                echo $form->field($model, 'eh_params')->widget(
                    \kdn\yii2\JsonEditor::class,
                    [
                        'clientOptions' => [
                            'modes' => ['code', 'form'],
                            'mode' => $model->isNewRecord ? 'code' : 'form'
                        ],
                        'expandAll' => ['tree', 'form'],
                    ]
                );
            } catch (Throwable $throwable) {
                echo $form->field($model, 'eh_params')->textarea(['rows' => 8, 'class' => 'form-control']);
                Yii::error(
                    AppHelper::throwableLog($throwable),
                    'EventHandlerController:eh_params:_form:notValidJson'
                );
            }
            ?>
        </div>
    </div>




    <?php /*= $form->field($model, 'eh_builder_json')->textInput()*/ ?>


    <div class="row">
        <div class="col-md-12 text-center">
            <div class="form-group">
                <?= Html::submitButton('<i class="fa fa-save"></i> Save Handler', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use frontend\helpers\JsonHelper;
use src\helpers\app\AppHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\eventManager\src\entities\EventList */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="event-list-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-6">



            <?php echo $form->field($model, 'el_key')->textInput(['maxlength' => true]) ?>

            <div class="row">
                <div class="col-md-6">
                    <?php /*= $form->field($model, 'el_object')->dropDownList(Yii::$app->event->objectList, ['prompt' => '-'])*/ ?>

                    <?php /* $form->field($model, 'el_key')->dropDownList(Yii::$app->event->objectList, ['prompt' => '-'])*/ ?>

                    <?= $form->field($model, 'el_category')->dropDownList(Yii::$app->event->categoryList, ['prompt' => '-']) ?>
                </div>
            </div>


            <?= $form->field($model, 'el_enable_log')->checkbox() ?>

            <?= $form->field($model, 'el_break')->checkbox() ?>




            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'el_enable_type')->dropDownList(\modules\eventManager\src\entities\EventList::getEnableTypeList()) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'el_cron_expression')->textInput(['maxlength' => true]) ?>
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
                        'https://crontab.guru/#' . str_replace(' ', '_', $model->el_cron_expression),
                        ['class' => 'btn btn-xs btn-warning', 'target' => '_blank']
) ?>


                </div>
            </div>
        </div>



            <?php /*= $form->field($model, 'el_condition')->textarea(['rows' => 6])*/ ?>

            <?php /*= $form->field($model, 'el_builder_json')->textInput()*/ ?>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'el_sort_order')->input(
                        'number',
                        ['min' => 0, 'max' => 1000, 'step' => 1]
                    ) ?>
                </div>
            </div>
            <?= $form->field($model, 'el_description')->textarea(['rows' => 3]) ?>
        </div>
        <div class="col-md-6">

            <?php
            $model->el_params = JsonHelper::encode($model->el_params);
            try {
                echo $form->field($model, 'el_params')->widget(
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
                echo $form->field($model, 'el_params')->textarea(['rows' => 8, 'class' => 'form-control']);
                Yii::error(
                    AppHelper::throwableLog($throwable),
                    'EventListController:el_params:_form:notValidJson'
                );
            }
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 text-center">
            <div class="form-group">
                <?= Html::submitButton('<i class="fa fa-save"></i> Save Event', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

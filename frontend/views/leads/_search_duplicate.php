<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\LeadSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-search">

    <?php $form = ActiveForm::begin([
        //'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <div class="row">
        <div class="col-md-3">

            <div class="row">
                <div class="col-md-4">

                </div>
                <div class="col-md-4">

                </div>
                <div class="col-md-4">

                </div>
            </div>

            <div class="row">
                <div class="col-md-4">

                </div>
                <div class="col-md-8">

                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'client_email')//->input('email') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'client_phone') ?>
                </div>
            </div>


        </div>



        <div class="col-md-3">




            <div class="row">
                <div class="col-md-6">
                    <?//php  echo $form->field($model, 'created_date_from') ?>

                    <?= $form->field($model, 'created_date_from')->widget(
                        \dosamigos\datepicker\DatePicker::class, [
                        'inline' => false,
                        //'template' => '<div class="well well-sm" style="background-color: #fff; width:250px">{input}</div>',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-M-yyyy',
                            'todayBtn' => true
                        ]
                    ]);?>

                </div>

                <div class="col-md-6">
                    <?//php  echo $form->field($model, 'created_date_to') ?>
                    <?= $form->field($model, 'created_date_to')->widget(
                        \dosamigos\datepicker\DatePicker::class, [
                        'inline' => false,
                        //'template' => '<div class="well well-sm" style="background-color: #fff; width:250px">{input}</div>',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-M-yyyy',
                            'todayBtn' => true
                        ]
                    ]);?>
                </div>
            </div>

            <?//= $form->field($model, 'employee_id') ?>


        </div>


    </div>

    <div class="row">
        <div class="col-md-12">
            <br>
            <div class="form-group text-center">
                <?= Html::submitButton('<i class="fa fa-search"></i> Search leads', ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton('<i class="fa fa-close"></i> Reset form', ['class' => 'btn btn-warning']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

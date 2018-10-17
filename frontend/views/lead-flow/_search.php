<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\LeadFlowSearch */
/* @var $form yii\widgets\ActiveForm */


if(Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)) {
    $userList = \common\models\Employee::getList();
} else {
    $userList = \common\models\Employee::getListByUserId(Yii::$app->user->id);
}

?>

<div class="lead-flow-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>


    <div class="row">

        <div class="col-md-3">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'lead_id')->input('number', ['min' => 0]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'employee_id')->dropDownList($userList, ['prompt' => '-']) ?>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="row">
                <div class="col-md-12">
                    <?//= $form->field($model, 'status')->dropDownList(\common\models\Lead::STATUS_LIST, ['prompt' => '-']) ?>
                    <?php
                    echo $form->field($model, 'statuses')->widget(\kartik\select2\Select2::class, [
                        'data' => \common\models\Lead::STATUS_LIST,
                        'size' => \kartik\select2\Select2::SMALL,
                        'options' => ['placeholder' => 'Select status', 'multiple' => true],
                        'pluginOptions' => ['allowClear' => true],
                    ]);
                    ?>

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
                            //'format' => 'dd-M-yyyy',
                            'format' => 'yyyy-mm-dd',
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
                            //'format' => 'dd-M-yyyy',
                            'format' => 'yyyy-mm-dd',
                            'todayBtn' => true
                        ]
                    ]);?>
                </div>
            </div>
        </div>
    </div>

    <?//= $form->field($model, 'created') ?>





    <?//= $form->field($model, 'status') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

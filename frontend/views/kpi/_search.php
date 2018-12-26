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
        <div class="col-md-4">

            <?= $form->field($model, 'sold_date_from')->widget(
                \dosamigos\datepicker\DatePicker::class, [
                'inline' => false,
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-M-yyyy',
                    'todayBtn' => true
                ]
            ])->label('Date from');?>

        </div>

        <div class="col-md-4">
            <?= $form->field($model, 'sold_date_to')->widget(
                \dosamigos\datepicker\DatePicker::class, [
                'inline' => false,
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-M-yyyy',
                    'todayBtn' => true
                ]
            ])->label('Date to');?>
        </div>

        <div class="col-md-4">
        	<?php
                if(Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id)) {
                    $userList = \common\models\Employee::getList();
                } else {
                    $userList = \common\models\Employee::getListByUserId(Yii::$app->user->id);
                }
            ?>
         	<?= $form->field($model, 'employee_id')->dropDownList($userList, ['prompt' => '-']) ?>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12">
            <br>
            <div class="form-group text-center">
                <?= Html::submitButton('<i class="fa fa-search"></i> Search', ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton('<i class="fa fa-close"></i> Reset form', ['class' => 'btn btn-warning']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
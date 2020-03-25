<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Employee;
use sales\entities\cases\CasesStatus;
use dosamigos\datepicker\DatePicker;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model sales\entities\cases\CaseStatusLogSearch */
/* @var $form yii\widgets\ActiveForm */

$userList = Employee::getList();

?>

<div class="case-status-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>


    <div class="row">

        <div class="col-md-3">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'csl_case_id')->input('number', ['min' => 0]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'csl_owner_id')->dropDownList($userList, ['prompt' => '-']) ?>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="row">
                <div class="col-md-12">
                    <?php
                    echo $form->field($model, 'statuses')->widget(Select2::class, [
                        'data' => CasesStatus::STATUS_LIST,
                        'size' => Select2::SMALL,
                        'options' => ['placeholder' => 'Select status', 'multiple' => true],
                        'pluginOptions' => ['allowClear' => true],
                    ])->label('Statuses');
                    ?>

                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'created_date_from')->widget(
                        DatePicker::class, [
                        'inline' => false,
                        'clientOptions' => [
                            'autoclose' => true,
                            //'format' => 'dd-M-yyyy',
                            'format' => 'yyyy-mm-dd',
                            'todayBtn' => true
                        ]
                    ]);?>
                </div>

                <div class="col-md-6">
                    <?= $form->field($model, 'created_date_to')->widget(
                        DatePicker::class, [
                        'inline' => false,
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

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

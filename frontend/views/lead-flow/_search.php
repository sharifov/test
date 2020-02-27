<?php

use common\models\Employee;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\LeadFlowSearch */
/* @var $form yii\widgets\ActiveForm */


/** @var Employee $user */
$user = Yii::$app->user->identity;

if($user->isAdmin()) {
    $userList = \common\models\Employee::getList();
} else {
    $userList = \common\models\Employee::getListByUserId($user->id);
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
                    <?php //= $form->field($model, 'status')->dropDownList(\common\models\Lead::STATUS_LIST, ['prompt' => '-']) ?>
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
                <div class="col-md-12">
                    <?= $form->field($model, 'createdRangeTime', [
                        'options' => ['class' => 'form-group']
                    ])->widget(\kartik\daterange\DateRangePicker::class, [
                        'presetDropdown' => false,
                        'hideInput' => true,
                        'convertFormat' => true,
                        'pluginOptions' => [
                            'timePicker' => true,
                            'timePickerIncrement' => 1,
                            'timePicker24Hour' => true,
                            'locale' => [
                                'format' => 'd-M-Y H:i',
                                'separator' => ' - '
                            ]
                        ]
                    ])->label('Created From / To');
                    ?>
                </div>
            </div>
        </div>
    </div>

    <?php //= $form->field($model, 'created') ?>
    <?php //= $form->field($model, 'status') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

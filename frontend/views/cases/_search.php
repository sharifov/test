<?php

use sales\access\EmployeeDepartmentAccess;
use sales\access\EmployeeProjectAccess;
use sales\entities\cases\CasesCategory;
use sales\entities\cases\CasesSourceType;
use sales\entities\cases\CasesStatus;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\entities\cases\CasesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cases-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-1">
                    <?= $form->field($model, 'cs_id') ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'cs_gid') ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'cs_project_id')->dropDownList(EmployeeProjectAccess::getProjects(), ['prompt' => '-']) ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'cs_dep_id')->dropDownList(EmployeeDepartmentAccess::getDepartments(), ['prompt' => '-']) ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'cs_category')->dropDownList(CasesCategory::getList(array_keys(EmployeeDepartmentAccess::getDepartments())), ['prompt' => '-']) ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'cs_status')->dropDownList(CasesStatus::STATUS_LIST, ['prompt' => '-']) ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'cs_subject') ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'cs_lead_id') ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'cs_created_dt')->widget(
                        \dosamigos\datepicker\DatePicker::class, [
                        'inline' => false,
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd-M-yyyy',
                        ]
                    ]);?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'cs_source_type_id')->dropDownList(CasesSourceType::getList(), ['prompt' => '']) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-1">
            <?= $form->field($model, 'cssSaleId') ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'cssBookId') ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'salePNR') ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'paxFirstName') ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'paxLastName') ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'clientPhone') ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'clientEmail') ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'airlineConfirmationNumber') ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'ticketNumber') ?>
        </div>
    </div>

    <div class="form-group text-center">
        <?= Html::submitButton('<i class="fa fa-search"></i> Search cases', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset form', ['cases/index'], ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

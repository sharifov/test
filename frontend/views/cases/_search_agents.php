<?php

use common\models\Airport;
use common\models\CaseSale;
use kartik\select2\Select2;
use sales\access\EmployeeDepartmentAccess;
use sales\access\EmployeeProjectAccess;
use sales\entities\cases\CaseCategory;
use sales\entities\cases\CasesSourceType;
use sales\entities\cases\CasesStatus;
use yii\helpers\ArrayHelper;
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
                    <?= $form->field($model, 'cssSaleId') ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'cssBookId') ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'salePNR') ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'cs_project_id')->dropDownList(EmployeeProjectAccess::getProjects(), ['prompt' => '-']) ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'cs_category_id')->dropDownList(CaseCategory::getList(array_keys(EmployeeDepartmentAccess::getDepartments())), ['prompt' => '-']) ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'cs_status')->dropDownList(CasesStatus::STATUS_LIST, ['prompt' => '-']) ?>
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
                <div class="col-md-1">
                    <?= $form->field($model, 'cs_source_type_id')->dropDownList(CasesSourceType::getList(), ['prompt' => '']) ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'cs_need_action')->dropDownList([1 => 'Yes', 0 => 'No'], ['prompt' => '']) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-1">
            <?php
                echo $form->field($model, 'departureAirport')->widget(Select2::class, [
                    'data' => Airport::getIataList(),
                    'size' => Select2::SMALL,
                    'options' => ['multiple' => true],
                    'pluginOptions' => ['allowClear' => true],
                ]);
            ?>
        </div>
        <div class="col-md-1">
            <?php
                echo $form->field($model, 'arrivalAirport')->widget(Select2::class, [
                    'data' => Airport::getIataList(),
                    'size' => Select2::SMALL,
                    'options' => ['multiple' => true],
                    'pluginOptions' => ['allowClear' => true],
                ]);
            ?>
        </div>
        <div class="col-md-1">
            <?php
                echo $form->field($model, 'departureCountries')->widget(Select2::class, [
                    'data' => Airport::getCountryList(),
                    'size' => Select2::SMALL,
                    'options' => ['multiple' => true],
                    'pluginOptions' => ['allowClear' => true],
                ]);
            ?>
        </div>
        <div class="col-md-1">
            <?php
                echo $form->field($model, 'arrivalCountries')->widget(Select2::class, [
                    'data' => Airport::getCountryList(),
                    'size' => Select2::SMALL,
                    'options' => ['multiple' => true],
                    'pluginOptions' => ['allowClear' => true],
                ]);
            ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'cssOutDate')->widget(
                \dosamigos\datepicker\DatePicker::class, [
                'inline' => false,
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-M-yyyy',
                ]
            ])  ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'cssInDate')->widget(
                \dosamigos\datepicker\DatePicker::class, [
                'inline' => false,
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-M-yyyy',
                ]
            ]) ?>
        </div>
        <div class="col-md-1">
            <?php
                $types = ArrayHelper::map(
                    CaseSale::find()->select('css_charge_type')->distinct()->where(['NOT', ['css_charge_type' => null]])->all(),
                    'css_charge_type','css_charge_type'
                )
            ?>
            <?= $form->field($model, 'cssChargeType')->dropDownList($types, ['prompt' => '---']) ?>
        </div>
    </div>

    <div class="form-group text-center">
        <?= Html::submitButton('<i class="fa fa-search"></i> Search cases', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset form', ['cases/index'], ['class' => 'btn btn-warning']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

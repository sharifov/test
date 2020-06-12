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
/* @var $dataProvider yii\data\ActiveDataProvider */
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
                    <?= $form->field($model, 'cs_category_id')->dropDownList(CaseCategory::getList(array_keys(EmployeeDepartmentAccess::getDepartments())), ['prompt' => '-']) ?>
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
        <div class="col-md-1">
            <?= $form->field($model, 'cs_need_action')->dropDownList([1 => 'Yes', 0 => 'No'], ['prompt' => '']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-1">
            <?= $form->field($model, 'clientId') ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'cssChargedFrom') ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'cssChargedTo') ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'cssProfitFrom') ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'cssProfitTo') ?>
        </div>
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
        <div class="col-md-2">
            <?= $form->field($model, 'cssInOutDate', [
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
                                'format' => 'd-M-Y',
                                'separator' => ' - '
                            ]
                        ]
                    ])->label('Flight Date From / To') ?>

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
        <div class="col-md-2">
			<?= $form->field($model, 'saleTicketSendEmailDate', [
				'options' => ['class' => 'form-group'],
			])->widget(
				\dosamigos\datepicker\DatePicker::class, [
				'inline' => false,
				'clientOptions' => [
					'autoclose' => true,
					'format' => 'yyyy-mm-dd',
				]
			])->label('Send Email Date') ?>

        </div>
    </div>

    <div class="form-group text-center">
        <?= Html::submitButton('<i class="fa fa-search"></i> Search cases', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset form', ['cases/index'], ['class' => 'btn btn-warning']) ?>
        <?php if ($model->saleTicketSendEmailDate) : ?>
			<?php echo \kartik\export\ExportMenu::widget([
				'dataProvider' => $dataProvider,
				'columns' => [
                    ['attribute' => 'cs_id', 'label' => 'Case Id'],
                    ['attribute' => 'cssSaleId', 'label' => 'Sale Id'],
                    ['attribute' => 'cssBookId', 'label' => 'Booking Id'],
                    ['attribute' => 'salePNR', 'label' => 'PNR'],
                    ['attribute' => 'saleTicketSendEmailDate'],
                    ['attribute' => 'sentEmailBy'],
                    ['attribute' => 'userGroup'],
                ],
				'exportConfig' => [
					\kartik\export\ExportMenu::FORMAT_PDF => [
						'pdfConfig' => [
							'mode' => 'c',
							'format' => 'A4-L',
						]
					]
				],
				'target' => \kartik\export\ExportMenu::TARGET_BLANK,
				'fontAwesome' => true,
				'dropdownOptions' => [
					'label' => 'Full Export'
				],
				'columnSelectorOptions' => [
					'label' => 'Export Fields'
				],
				'showConfirmAlert' => false,
				'options' => [
					'id' => 'export-links'
				],
			]); ?>
        <?php endif; ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>

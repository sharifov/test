<?php

use common\models\Airports;
use common\models\CaseSale;
use dosamigos\multiselect\MultiSelect;
use kartik\select2\Select2;
use src\access\EmployeeDepartmentAccess;
use src\access\EmployeeProjectAccess;
use src\entities\cases\CaseCategory;
use src\entities\cases\CasesSearch;
use src\entities\cases\CasesSourceType;
use src\entities\cases\CasesStatus;
use src\model\saleTicket\entity\SaleTicket;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use common\models\Language;

/* @var $this yii\web\View */
/* @var $model src\entities\cases\CasesSearch */
/* @var $form yii\widgets\ActiveForm */
/* @var $dataProvider yii\data\ActiveDataProvider */

$select2Properties = [
    'size' => Select2::SIZE_SMALL,
    'options' => [
        'placeholder' => 'Select location ...',
        'multiple' => true,
    ],
    'pluginOptions' => [
        'allowClear' => true,
        'minimumInputLength' => 1,
        'language' => [
            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
        ],
        'ajax' => [
            'url' => ['/airport/get-list'],
            'dataType' => 'json',
            'data' => new JsExpression('function(params) { return {term:params.term}; }'),
        ],
        'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
        'templateResult' => new JsExpression('formatRepo'),
        'templateSelection' => new JsExpression('function (data) { return data.selection || data.text;}'),
    ]
];
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
                    <?php echo $form->field($model, 'csStatuses')
                        ->widget(
                            MultiSelect::class,
                            [
                                'data' => CasesStatus::STATUS_LIST,
                                'options' => ['multiple' => 'multiple'],
                                'clientOptions' => ['numberDisplayed' => 1],
                            ]
                        )
                    ?>
                </div>
                <div class="col-md-1">
                    <?= $form->field($model, 'clientId') ?>
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
                <div class="col-md-2">
                        <?php echo $form->field($model, 'locales')->widget(Select2::class, [
                            'data' => Language::getLocaleList(false),
                            'size' => Select2::SMALL,
                            'options' => ['id' => 'locales',
                                'multiple' => true],
                            'pluginOptions' => ['allowClear' => true],
                        ]);
?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-1">
            <?php
                /* echo $form->field($model, 'departureAirport')->widget(Select2::class, [
                    'data' => Airports::getIataList(),
                    'size' => Select2::SMALL,
                    'options' => ['multiple' => true],
                    'pluginOptions' => ['allowClear' => true],
                ]); */
            ?>

            <?= $form->field($model, 'departureAirport')->widget(Select2::class, ArrayHelper::merge($select2Properties, [
                'value' => static function (CasesSearch $model) {
                    return array_values($model->departureAirport);
                },
            ])) ?>
        </div>
        <div class="col-md-1">
            <?php
                 /*echo $form->field($model, 'arrivalAirport')->widget(Select2::class, [
                    'data' => Airports::getIataList(),
                    'size' => Select2::SMALL,
                    'options' => ['multiple' => true],
                    'pluginOptions' => ['allowClear' => true],
                ]);*/
            ?>
            <?= $form->field($model, 'arrivalAirport')->widget(Select2::class, ArrayHelper::merge($select2Properties, [
                'value' => static function (CasesSearch $model) {
                    return array_values($model->arrivalAirport);
                },
            ])) ?>
        </div>
        <div class="col-md-1">
            <?php
                echo $form->field($model, 'departureCountries')->widget(Select2::class, [
                    'data' => Airports::getCountryList(),
                    'size' => Select2::SMALL,
                    'options' => ['multiple' => true],
                    'pluginOptions' => ['allowClear' => true],
                ]);
                ?>
        </div>
        <div class="col-md-1">
            <?php
                echo $form->field($model, 'arrivalCountries')->widget(Select2::class, [
                    'data' => Airports::getCountryList(),
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
            <?= $form->field($model, 'cssChargeType')->dropDownList(CaseSale::getChargeTypesList(), ['prompt' => '---']) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'saleTicketSendEmailDate', [
                'options' => ['class' => 'form-group'],
            ])->widget(
                \dosamigos\datepicker\DatePicker::class,
                [
                'inline' => false,
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                ]
                ]
            )->label('Send Email Date') ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'airlinePenalty')->dropDownList(SaleTicket::getAirlinePenaltyList(), ['prompt' => '---']) ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'validatingCarrier')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-1">
            <?= $form->field($model, 'callsQtyFrom')->textInput() ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'callsQtyTo')->textInput() ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'smsQtyFrom')->textInput() ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'smsQtyTo')->textInput() ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'emailsQtyFrom')->textInput() ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'emailsQtyTo')->textInput() ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'chatsQtyFrom')->textInput() ?>
        </div>
        <div class="col-md-1">
            <?= $form->field($model, 'chatsQtyTo')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h2><i class="fa fa-list"></i> Show Additional fields</h2>
            <?= $form->field($model, 'showFields')->widget(Select2::class, [
                'data' => $model->getViewFields(),
                'size' => Select2::SIZE_SMALL,
                'pluginOptions' => [
                    'closeOnSelect' => false,
                    'allowClear' => true,
                ],
                'options' => [
                    'placeholder' => 'Choose additional fields...',
                    'multiple' => true,
                    'id' => 'showFields',
                ],
            ])->label(false) ?>
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
                'bsVersion' => '3.x',
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

<?php
$js = <<<JS
function formatRepo( repo ) {
    if (repo.loading) return repo.text;
    var markup = "<div class='select2-result-repository clearfix'>" +
        "<div class='select2-result-repository__meta'>" +
            "<div class='select2-result-repository__title'>" + repo.text + "</div>";
    markup +=	"</div></div>";

    return markup;
}
JS;
$this->registerJs($js, \yii\web\View::POS_HEAD);

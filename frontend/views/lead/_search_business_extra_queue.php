<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Lead;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\search\LeadSearch */
/* @var $form yii\widgets\ActiveForm */

$select2Properties = [
    'size' => Select2::SIZE_SMALL,
    'options' => [
        'placeholder' => 'Select location ...',
        'multiple' => false,
    ],
    'pluginOptions' => [
        'allowClear' => true,
        'minimumInputLength' => 2,
        'language' => [
            'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
        ],
        'ajax' => [
            'url' => ['/airport/get-list'],
            'dataType' => 'json',
            'data' => new JsExpression('function(params) { return {term:params.term}; }'),
        ],
        'escapeMarkup' => new JsExpression('function (select2markup) { return select2markup; }'),
        'templateResult' => new JsExpression('formatRepo'),
        'templateSelection' => new JsExpression('function (data) { return data.selection || data.text;}'),
    ]
];
?>

    <div class="lead-search-bonus">

        <?php

        $form = ActiveForm::begin([
            'id' => 'lead-search-bonus-form',
            'method' => 'get',
            'options' => [
                'data-pjax' => 1,
            ]
        ]);

        ?>

        <div class="row">
            <div class="col-md-12 col-sm-12  profile_details">
                <div class="well profile_view">
                    <div class="col-sm-12">
                        <h3 class="brief" style="margin-bottom:20px;"><i>Trip</i></h3>
                        <div class="row">

                            <div class="col-md-5">
                                <?= $form->field($model, 'origin_airport')->widget(Select2::class, $select2Properties) ?>
                            </div>
                            <div class="col-md-5">
                                <?= $form->field($model, 'destination_airport')->widget(Select2::class, $select2Properties) ?>
                            </div>
                            <div class="col-md-5">
                                <?= $form->field($model, 'departRangeTime', ['options' => ['class' => 'form-group']])->widget(\kartik\daterange\DateRangePicker::class, [
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
                                ])->label('Depart From / To'); ?>
                            </div>
                            <div class="col-md-5">
                                <?= $form->field($model, 'email_status')->dropDownList([1 => 'WithOut email', 2 => 'With email'], ['prompt' => '-']) ?>
                            </div>
                            <div class="col-md-5">
                                <?= $form->field($model, 'quote_status')->dropDownList([1 => 'Not send quotes', 2 => 'Send quotes'], ['prompt' => '-']) ?>
                            </div>
                        </div>
                    </div>
                    <div class=" profile-bottom text-center">
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <br>
                <div class="form-group text-center">
                    <?= Html::submitButton('<i class="fa fa-search"></i> Search leads', ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('<i class="glyphicon glyphicon-repeat"></i> Reset data', ['lead/business-extra-queue'], ['class' => 'btn btn-warning']) ?>
                </div>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

<?php
$js = <<<JS
function formatRepo( repo ) {
				if (repo.loading) return repo.text;

				var select2markup = "<div class='select2-result-repository clearfix'>" +
					"<div class='select2-result-repository__meta'>" +
						"<div class='select2-result-repository__title'>" + repo.text + "</div>";
                
				select2markup +=	"</div></div>";

				return select2markup;
			}
JS;
$this->registerJs($js, \yii\web\View::POS_HEAD);

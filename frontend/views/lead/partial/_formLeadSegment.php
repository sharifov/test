<?php
/**
 * @var $form ActiveForm
 * @var $segment \sales\forms\lead\SegmentEditForm
 */

use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;
use yii\web\JsExpression;
use \kartik\select2\Select2;
use yii\web\View;
use \sales\helpers\lead\LeadFlightSegmentHelper;
use \unclead\multipleinput\MultipleInput;

$select2Properties = [
    'options' => [
        'placeholder' => 'Select location ...',
        'multiple' => false,
    ],
    'pluginOptions' => [
        'width' => '100%',
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

<?= $form->field($segment, 'segments')->widget(MultipleInput::class, [
    'max' => 10,
    'cloneButton' => true,
    'enableError' => true,
    'columns' => [
        [
            'name' => 'origin',
            //'type' => Select2::class,
            'title' => 'Origin Location',
            'value' => function ($segment) {
                return $segment['origin'];
            },
            'options' => function ($segment) use ($select2Properties) {
                $select2Properties['data'] = [$segment['origin'] => $segment['originLabel']];
                return $select2Properties;
            },
        ],
        [
            'name' => 'destination',
            'type' => Select2::class,
            'title' => 'Destination',
            'value' => function ($segment) {
                return $segment['destination'];
            },
            'options' => function ($segment) use ($select2Properties) {
                $select2Properties['data'] = [$segment['destination'] => $segment['destinationLabel']];
                return $select2Properties;
            },
        ],
        [
            'name' => 'departure',
//            'type' => DatePicker::class,
            'title' => 'Departure',
            'value' => function ($segment) {
                return date('d-M-Y', strtotime($segment['departure']));
            },
            'enableError' => true,
            'options' => [
                'clientOptions' => [
                    'inline' => false,
                    'autoclose' => true,
                    'format' => 'dd-M-yyyy',
                    'todayHighlight' => true,
                    //'defaultValue' => date('d-M-Y')
                ],
                'options' => [
                    'class' => 'depart-date form-control',
                    'placeholder' => 'Departing Date',
                    'readonly' => true
                ],
            ]
        ],
        [
            'name' => 'flexibility',
            'type' => 'dropDownList',
            'title' => 'Flex (days)',
            'value' => function ($segment) {
                return $segment['flexibility'];
            },
            'items' => LeadFlightSegmentHelper::flexibilityList()
        ],
        [
            'name' => 'flexibilityType',
            'type' => 'dropDownList',
            'title' => 'Flex (+/-)',
            'value' => function ($segment) {
                return $segment['flexibilityType'];
            },
            'items' => LeadFlightSegmentHelper::flexibilityTypeList()
        ],
    ]
])->label(false) ?>

<?php

$js = <<<JS
function formatRepo( repo ) {
				if (repo.loading) return repo.text;

				var markup = "<div class='select2-result-repository clearfix'>" +
					"<div class='select2-result-repository__avatar'><img src='/img/incoming-call.png' /></div>" +
					"<div class='select2-result-repository__meta'>" +
						"<div class='select2-result-repository__title'>" + repo.text + "</div>";
				
				markup += "<div class='select2-result-repository__statistics'>" +
							"<div class='select2-result-repository__forks'><span class='glyphicon glyphicon-flash'></span> " + repo.id + " Forks</div>" +
						"</div>" +
					"</div></div>";

				return markup;
			}
JS;
$this->registerJs($js, View::POS_HEAD);

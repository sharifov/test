<?php

use kartik\select2\Select2;
use modules\flight\models\forms\ItineraryEditForm;
use dosamigos\datepicker\DatePicker;
use modules\flight\src\helpers\FlightSegmentHelper;
use unclead\multipleinput\MultipleInput;
use yii\bootstrap4\ActiveForm;
use yii\web\JsExpression;
use yii\web\View;

/**
 * @var View $this
 * @var ItineraryEditForm $model
 * @var ActiveForm $form
 */



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

<?= $form->field($model, 'segments')->widget(MultipleInput::class, [
	'max' => 10,
//    'allowEmptyList' => true,
	'enableError' => true,
	'showGeneralError' => true,
	'columns' => [
		[
			'name' => 'fs_origin_iata',
			'type' => Select2::class,
			'title' => 'Origin',
			'value' => static function ($segment) {
				return $segment['fs_origin_iata'] ?? '';
			},
			'options' => static function ($segment) use ($select2Properties) {
				if (isset($segment['fs_origin_iata'])) {
					$select2Properties['data'] = [$segment['fs_origin_iata'] => $segment['fs_origin_iata_label']];
				} else {
					$select2Properties['data'] = [];
				}
				return $select2Properties;
			},
			'headerOptions' => [
				//'style' => 'width: 35%;',
			]
		],
		[
			'name' => 'fs_destination_iata',
			'type' => Select2::class,
			'title' => 'Destination',
			'value' => static function ($segment) {
				return $segment['fs_destination_iata'] ?? '';
			},
			'options' => static function ($segment) use ($select2Properties) {
				if (isset($segment['fs_destination_iata'])) {
					$select2Properties['data'] = [$segment['fs_destination_iata'] => $segment['fs_destination_iata_label']];
				} else {
					$select2Properties['data'] = [];
				}
				return $select2Properties;
			},

		],
		[
			'name' => 'fs_departure_date',
			'type' => DatePicker::class,
			'title' => 'Departure',
			'value' => static function ($segment) {
				return (isset($segment['fs_departure_date']) && $segment['fs_departure_date']) ? date('d-M-Y', strtotime($segment['fs_departure_date'])) : date('d-M-Y');
			},
			'options' => [
				'addon' => '',
				'clientOptions' => [
					'autoclose' => true,
					'format' => 'dd-M-yyyy',
					'todayHighlight' => true,
					'sty'
				],
				'options' => [
					'class' => 'depart-date form-control',
					'placeholder' => 'Departure',
					'readonly' => true,
				],
			],
			'headerOptions' => [
				'style' => 'width: 130px;',
			]
		],
		[
			'name' => 'fs_flex_type_id',
			'type' => 'dropDownList',
			'title' => 'Flex (+/-)',
			'value' => static function ($segment) {
				return $segment['fs_flex_type_id'] ?? '';
			},
			'items' => FlightSegmentHelper::flexibilityTypeList(),
			'headerOptions' => [
				'style' => 'width: 80px;',
			]
		],

		[
			'name' => 'fs_flex_days',
			'type' => 'dropDownList',
			'title' => 'Flex (days)',
			'value' => static function ($segment) {
				return $segment['fs_flex_days'] ?? '';
			},
			'items' => FlightSegmentHelper::flexibilityList(),
			'headerOptions' => [
				'style' => 'width: 80px;',
			]
		],

	]
])->label(false) ?>

<?php

$js = <<<JS
function formatRepo( repo ) {
				if (repo.loading) return repo.text;

				var markup = "<div class='select2-result-repository clearfix'>" +
					//"<div class='select2-result-repository__avatar'><i class=\"fa fa-plane\"></div>" +
					"<div class='select2-result-repository__meta'>" +
						"<div class='select2-result-repository__title'>" + repo.text + "</div>";

				/*markup += "<div class='select2-result-repository__statistics'>" +
							"<div class='select2-result-repository__forks'>" + repo.id + "</div>" +
						"</div>" +*/
				markup +=	"</div></div>";

				return markup;
			}
JS;
$this->registerJs($js, View::POS_HEAD);

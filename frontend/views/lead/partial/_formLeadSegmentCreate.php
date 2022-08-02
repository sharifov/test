<?php

/**
 * @var $form ActiveForm
 * @var $segment src\forms\lead\SegmentEditForm
 * @var $model src\forms\lead\ItineraryEditForm
 */

use modules\featureFlag\FFlag;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;
use yii\web\JsExpression;
use kartik\select2\Select2;
use yii\web\View;
use src\helpers\lead\LeadFlightSegmentHelper;
use unclead\multipleinput\MultipleInput;

$select2Properties = [
    'options' => [
        'placeholder' => 'Select location ...',
        'multiple' => false,
    ],
    'pluginOptions' => [
//      'width' => '100%',
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
//  'rendererClass' => \unclead\multipleinput\renderers\ListRenderer::class,
////    'addButtonPosition' => MultipleInput::,
    'id' => 'default-flight-multiple-input',
    'layoutConfig' => [
        'labelClass' => 'col-md-2',
        'wrapperClass' => 'col-md-8',
        'errorClass' => 'col-md-12',
        'buttonActionClass' => 'col-md-2',
    ],
    'max' => 10,
//    'allowEmptyList' => true,
    'enableError' => true,
    'showGeneralError' => true,
    'columns' => [
        [
            'name' => 'origin',
            'type' => Select2::class,
            'title' => 'Origin',
            'value' => static function ($segment) {
                return $segment['origin'] ?? '';
            },
            'options' => function ($segment) use ($select2Properties) {
                $select2Properties['data'] = [];
                if (isset($segment['origin'])) {
                    $select2Properties['data'] = [$segment['origin'] => $segment['originLabel']];
                }
                return $select2Properties;
            },
            'headerOptions' => [
                //'style' => 'width: 35%;',
            ]
        ],
        [
            'name' => 'destination',
            'type' => Select2::class,
            'title' => 'Destination',
            'value' => static function ($segment) {
                return $segment['destination'] ?? '';
            },
            'options' => function ($segment) use ($select2Properties) {
                $select2Properties['data'] = [];
                if (isset($segment['destination'])) {
                    $select2Properties['data'] = [$segment['destination'] => $segment['destinationLabel']];
                }
                return $select2Properties;
            },

        ],
        [
            'name' => 'departure',
            'type' => DatePicker::class,
            'title' => 'Departure',
            'value' => static function ($segment) {
                return (isset($segment['departure']) && $segment['departure']) ? date('d-M-Y', strtotime($segment['departure'])) : date('d-M-Y');
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
            'name' => 'flexibilityType',
            'type' => 'dropDownList',
            'title' => 'Flex (+/-)',
            'value' => static function ($segment) {
                return $segment['flexibilityType'] ?? '';
            },
            'items' => LeadFlightSegmentHelper::flexibilityTypeList(),
            'headerOptions' => [
//              'style' => 'width: 80px;',
            ]
        ],

        [
            'name' => 'flexibility',
            'type' => 'dropDownList',
            'title' => 'Flex (days)',
            'value' => static function ($segment) {
                return $segment['flexibility'] ?? '';
            },
            'items' => LeadFlightSegmentHelper::flexibilityList(),
            'headerOptions' => [
//              'style' => 'width: 80px;',
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



$returnFlightAutocomplete = <<<JS_RFA
function setLastDataToNewElement(lastElement, curElement) {
    lastElement.select2('data').forEach((e) => {
        let title = '';
        
        if ('selection' in e) {
            title = e.selection;
        } else {
            title = e.text;
        }
        
        let newOption = new Option(title, e.id, false, false);
        curElement.append(newOption).trigger('change');
    });
    
    curElement.val(lastElement.val()).trigger('change');
}
$('#default-flight-multiple-input').on('afterAddRow', function(e, row, currentIndex) {
    let block = $(e.currentTarget),
        items = block.find('.multiple-input-list__item');
    
    if (items.length === 2) {
        let lastElemNum = items.length - 2,
            lastDestination = items.eq(lastElemNum).find('[name $= "[destination]"]'),
            curDestination = items.last().find('[name $= "[destination]"]'),
            lastOrigin = items.eq(lastElemNum).find('[name $= "[origin]"]'),
            curOrigin = items.last().find('[name $= "[origin]"]');
        
        setLastDataToNewElement(lastDestination, curOrigin);
        setLastDataToNewElement(lastOrigin, curDestination);
    }
});
JS_RFA;

/** @fflag FFlag::FF_KEY_RETURN_FLIGHT_SEGMENT_AUTOCOMPLETE_ENABLE, Return flight segment autocomplete enable */
if (Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_RETURN_FLIGHT_SEGMENT_AUTOCOMPLETE_ENABLE)) {
    $this->registerJs($returnFlightAutocomplete, View::POS_READY);
}
<?php

use common\models\Airline;
use dosamigos\datetimepicker\DateTimePicker;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\QuoteSegment */
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


<div class="quote-segment-form row">

    <div class="col-md-6">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'qs_trip_id')->textInput() ?>

        <?= $form->field($model, 'qs_departure_time')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'qs_arrival_time')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'qs_stop')->textInput() ?>

        <?= $form->field($model, 'qs_flight_number')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'qs_booking_class')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'qs_duration')->textInput() ?>

        <?= $form->field($model, 'qs_departure_airport_code')->widget(Select2::class, $select2Properties) ?>

        <?= $form->field($model, 'qs_departure_airport_terminal')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'qs_arrival_airport_code')->widget(Select2::class, $select2Properties) ?>

        <?= $form->field($model, 'qs_arrival_airport_terminal')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'qs_operating_airline')->widget(Select2::class, [
            'data' => Airline::getAirlinesMapping(true),
            'size' => Select2::SIZE_SMALL,
            'options' => [
                'placeholder' => 'Select ...',
                'multiple' => false,
            ],
        ]) ?>

        <?= $form->field($model, 'qs_marketing_airline')->widget(Select2::class, [
            'data' => Airline::getAirlinesMapping(true),
            'size' => Select2::SIZE_SMALL,
            'options' => [
                'placeholder' => 'Select ...',
                'multiple' => false,
            ],
        ]) ?>

        <?= $form->field($model, 'qs_air_equip_type')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'qs_marriage_group')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'qs_mileage')->textInput() ?>

        <?= $form->field($model, 'qs_cabin')->dropDownList(\common\models\QuoteSegment::getCabin()) ?>

        <?= $form->field($model, 'qs_meal')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'qs_fare_code')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'qs_key')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'qs_ticket_id')->textInput() ?>

        <?= $form->field($model, 'qs_recheck_baggage')->checkbox() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>


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

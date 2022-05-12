<?php

use common\models\Airports;
use frontend\extensions\DatePicker;
use kartik\select2\Select2;
use src\widgets\DateTimePicker;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\QuoteSegmentStop */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="quote-segment-stop-form row">
    <div class="col-md-3">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'qss_location_code')->widget(Select2::class, [
            'value' => static function ($model) {
                return $model->qss_location_code ?? '';
            },
            'options' => [
                'placeholder' => 'Select location ...',
                'multiple' => false,
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
        ]) ?>

        <?= $form->field($model, 'qss_departure_dt')->widget(DateTimePicker::class, [
            'clientOptions' => [
                'autoclose' => true,
            ]
        ]) ?>

        <?= $form->field($model, 'qss_arrival_dt')->widget(DateTimePicker::class, [
            'clientOptions' => [
                'autoclose' => true,
            ]
        ]) ?>

        <?= $form->field($model, 'qss_duration')->textInput() ?>

        <?= $form->field($model, 'qss_elapsed_time')->textInput() ?>

        <?= $form->field($model, 'qss_equipment')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'qss_segment_id')->textInput() ?>

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

				var markup = "<div class='select2-result-repository clearfix'>" +
					"<div class='select2-result-repository__meta'>" +
						"<div class='select2-result-repository__title'>" + repo.text + "</div>";
				markup +=	"</div></div>";

				return markup;
			}
JS;
$this->registerJs($js, View::POS_HEAD);

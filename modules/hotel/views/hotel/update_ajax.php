<?php

use dosamigos\datepicker\DatePicker;
use kartik\select2\Select2;
use modules\hotel\models\forms\HotelForm;
use modules\hotel\models\Hotel;
use yii\web\View;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model HotelForm */

$pjaxId = 'pjax-hotel-update'
?>
<div class="hotel-update-ajax">
    <div class="hotel-form">
        <script>
            pjaxOffFormSubmit('#<?=$pjaxId?>');
        </script>
        <?php \yii\widgets\Pjax::begin(['id' => $pjaxId, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
        <?php
        $form = ActiveForm::begin([
            'options' => ['data-pjax' => true],
            'action' => ['/hotel/hotel/update-ajax', 'id' => $model->ph_id],
            'method' => 'post',
            'enableClientValidation' => false
        ]);
        ?>

		    <?= $form->errorSummary($model) ?>

            <?= $form->field($model, 'ph_check_in_date')->widget(
				DatePicker::class, [
					'inline' => false,
					'clientOptions' => [
						'autoclose' => true,
						'format' => 'yyyy-mm-dd',
						'todayBtn' => true
					]
            ])?>


            <?= $form->field($model, 'ph_check_out_date')->widget(
				DatePicker::class, [
                'inline' => false,
                'clientOptions' => [
                    'autoclose' => true,
					'format' => 'yyyy-mm-dd',
					'todayBtn' => true
                ]
            ])?>

            <div class="form-group" id="update_hotel_request_dest_type_wrap">
                <label>Destination Type</label>
                <?= Select2::widget([
                    'name' => 'ph_destination_type',
                    'data' => Hotel::getDestinationList(),
                    'options' => [
                        'placeholder' => 'Select destination type...',
                        'multiple' => true,
                        'id' => 'ph_destination_type'
                    ],
					'size' => Select2::SIZE_SMALL,
				]) ?>
            </div>


            <?= $form->field($model, 'ph_destination_label')->widget(Select2::class, [
                'options' => [
                    'placeholder' => $model->getAttributeLabel('ph_destination_label')
                ],
				'pluginOptions' => [
					'allowClear' => true,
					'minimumInputLength' => 2,
					'language' => [
						'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
					],
					'ajax' => [
						'url' => ['/hotel/hotel/ajax-get-destination-list'],
						'dataType' => 'json',
						'data' => new JsExpression('function(params) { 
						    return {term:params.term, destType: $("#ph_destination_type").val()}; 
						}'),
                        'delay' => 1200,
					],
					'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
					'templateResult' => new JsExpression('formatRepo'),
					'templateSelection' => new JsExpression('function (data) { return data.selection || data.text;}'),
				],
                'pluginEvents' => [
                    'select2:select' => new JsExpression('function (data) { 
                        $("#ph_destination_code").val(data.params.data.code); 
                        $("#ph_zone_code").val(data.params.data.zone_code); 
                        $("#ph_hotel_code").val(data.params.data.hotel_code); 
                    }')
                ]
            ]) ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'ph_min_star_rate')->dropDownList(array_combine(range(1, 5), range(1, 5)), ['prompt' => '-']) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'ph_max_star_rate')->dropDownList(array_combine(range(1, 5), range(1, 5)), ['prompt' => '-']) ?>
                </div>

                <div class="col-md-6">
                    <?= $form->field($model, 'ph_min_price_rate')->input('number', ['min' => 0]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'ph_max_price_rate')->input('number', ['min' => 0]) ?>
                </div>
            </div>


            <?= $form->field($model, 'ph_destination_code')->hiddenInput([
                'id' => 'ph_destination_code'
            ])->label(false) ?>

            <?= $form->field($model, 'ph_zone_code')->hiddenInput([
                'id' => 'ph_zone_code'
            ])->label(false) ?>

            <?= $form->field($model, 'ph_hotel_code')->hiddenInput([
                'id' => 'ph_hotel_code'
            ])->label(false) ?>

            <div class="form-group text-center">
                <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success']) ?>
            </div>

        <?php ActiveForm::end(); ?>
        <?php \yii\widgets\Pjax::end(); ?>
    </div>
</div>

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
?>

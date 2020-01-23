<?php

use kartik\select2\Select2;
use modules\flight\models\Flight;
use modules\flight\models\FlightQuote;
use modules\flight\src\useCases\api\searchQuote\FlightQuoteSearchForm;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/**
 * @var $result array
 * @var $minPrice int
 * @var $maxPrice int
 * @var $minTotalDuration int
 * @var $maxTotalDuration int
 * @var $airlines array
 * @var $flight Flight
 * @var $searchFrom FlightQuoteSearchForm
 */
?>

<div class="row">
    <div class="col-md-12">
        <?php $form = ActiveForm::begin([
                'options' => [
                    'data-pjax' => 1
                ],
        ]) ?>

        <div class="row">
            <div class="col-md-3">
                <?= $form->field($searchFrom, 'fareType', [
                    'labelOptions' => [
                        'class' => 'control-label'
                    ]
                ])->widget(Select2::class, [
                    'options' => [
                        'placeholder' => $searchFrom->getAttributeLabel('fareType'),
						'multiple' => true,
					],
                    'data' => FlightQuote::getFareTypeList(),
                    'size' => Select2::SIZE_SMALL
                ]) ?>
            </div>

            <div class="col-md-9">
				<?= $form->field($searchFrom, 'airlines', [
					'labelOptions' => [
						'class' => 'control-label'
					]
				])->widget(Select2::class, [
					'options' => [
						'placeholder' => $searchFrom->getAttributeLabel('airlines'),
						'multiple' => true,
					],
					'data' => $airlines,
					'size' => Select2::SIZE_SMALL
				]) ?>
            </div>

            <div class="col-md-1">
                <?= $form->field($searchFrom, 'price', [
                    'labelOptions' => [
                        'class' => 'control-label'
                    ]
                ])->input('number', [
                    'max' => $maxPrice,
                    'min' => $minPrice,
                    'autocomplete' => false,
                    'step' => 0.01
                ])->label('Max Price') ?>
            </div>

            <div class="col-md-2">
				<?= $form->field($searchFrom, 'stops', [
					'labelOptions' => [
						'class' => 'control-label'
					],
				])->dropDownList(FlightQuote::getStopsLIst(), [
                    'prompt' => '--'
                ]) ?>
            </div>

            <div class="col-md-2">
                <?= $form->field($searchFrom, 'airportChange', [
					'labelOptions' => [
						'class' => 'control-label'
					]
                ])->dropDownList(FlightQuote::getChangeAirportList()) ?>
            </div>

            <div class="col-md-2">
                <?= $form->field($searchFrom, 'baggage', [
					'labelOptions' => [
						'class' => 'control-label'
					]
                ])->dropDownList(FlightQuote::getBaggageList()) ?>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <div class="d-flex align-items-center justify-content-between">
                        <label for="" class="control-label">Max Trip Duration</label>
                        <span id="current-duration-value"></span>
                    </div>
                    <div class="d-flex justify-content-center align-items-center" style="width: 100%; height: 100%;">
                        <div class="search-filters__slider" id="duration-slider-filter"></div>
                    </div>
                    <?= $form->field($searchFrom, 'tripDuration')->hiddenInput()->label(false) ?>
                    <script>
                        var min = <?= $minTotalDuration ?? 0 ?>;
                        var max = <?= $maxTotalDuration ?? 0 ?>;

                        var start = '<?= $searchFrom->tripDuration ?>' || max;
                        var sliderDuration = $('#duration-slider-filter')[0];
                        noUiSlider.create(sliderDuration, {
                            start: [start],
                            connect: [true, false],
                            tooltips: {
                                to: function(value){
                                    return window.helper.toHHMM(value * 60);
                                }
                            },
                            step: 15,
                            range: {
                                'min': min,
                                'max': max
                            }
                        });

                        sliderDuration.noUiSlider.on('update', function (values, handle) {
                            $('#duration-slider-filter').closest('.form-group').find('input').val(values[handle]);
                            $('#duration-slider-filter').closest('.form-group').find('#current-duration-value').html(window.helper.toHHMM(values[handle] * 60));
                        });
                    </script>
                </div>
            </div>

            <div class="col-md-2">
				<?= $form->field($searchFrom, 'sortBy', [
					'labelOptions' => [
						'class' => 'control-label'
					]
				])->dropDownList(FlightQuote::getSortList()) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 d-flex align-items-center justify-content-center">
				<?= Html::submitButton('<i class="fa fa-filter"></i> Apply filter', [
					'class' => 'btn btn-success',
                    'id' => 'flight-quote-search-submit'
				]) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
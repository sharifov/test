<?php

use common\models\Quote;
use frontend\helpers\QuoteHelper;
use kartik\select2\Select2;
use sales\forms\api\searchQuote\FlightQuoteSearchForm;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/**
 * @var $minPrice int
 * @var $maxPrice int
 * @var $minTotalDuration int
 * @var $maxTotalDuration int
 * @var $airlines array
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
                        'class' => 'control-label',
                    ]
                ])->widget(Select2::class, [
                    'options' => [
                        'placeholder' => $searchFrom->getAttributeLabel('fareType'),
                        'multiple' => true,
                        'id' => 'search-quote-fare-type'
                    ],
                    'data' => Quote::getFareTypeList(),
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
                        'id' => 'search-quote-airlines'
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
                ])->dropDownList(Quote::getStopsLIst(), [
                    'prompt' => '--'
                ]) ?>
            </div>

            <div class="col-md-2">
                <?= $form->field($searchFrom, 'airportChange', [
                    'labelOptions' => [
                        'class' => 'control-label'
                    ]
                ])->dropDownList(Quote::getChangeAirportList()) ?>
            </div>

            <div class="col-md-2">
                <?= $form->field($searchFrom, 'baggage', [
                    'labelOptions' => [
                        'class' => 'control-label'
                    ]
                ])->dropDownList(Quote::getBaggageList()) ?>
            </div>

            <div class="col-md-2">
                <div class="form-group">
                    <div class="d-flex align-items-center justify-content-between">
                        <label for="" class="control-label">Max Trip Duration</label>
                        <span id="search-quote-current-duration-value"></span>
                    </div>
                    <div class="d-flex justify-content-center align-items-center" style="width: 100%; height: 100%;">
                        <div class="search-filters__slider" id="search-quote-duration-slider-filter"></div>
                    </div>
                    <?= $form->field($searchFrom, 'tripDuration')->hiddenInput()->label(false) ?>
                    <script>
                        var min = <?= $minTotalDuration ?? 0 ?>;
                        var max = <?= $maxTotalDuration ?? 0 ?>;

                        var start = '<?= $searchFrom->tripDuration ?>' || max;
                        var sliderDuration = $('#search-quote-duration-slider-filter')[0];
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
                            $('#search-quote-duration-slider-filter').closest('.form-group').find('input').val(values[handle]);
                            $('#search-quote-duration-slider-filter').closest('.form-group').find('#search-quote-current-duration-value').html(window.helper.toHHMM(values[handle] * 60));
                        });
                    </script>
                </div>
            </div>

            <div class="col-md-2">
                <?= $form->field($searchFrom, 'sortBy', [
                    'labelOptions' => [
                        'class' => 'control-label'
                    ]
                ])->dropDownList(Quote::getSortList()) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <?= $form->field($searchFrom, 'topCriteria', [
                    'labelOptions' => [
                        'class' => 'control-label'
                    ]
                ])->dropDownList(QuoteHelper::TOP_META_LIST, [
                    'prompt' => '--'
                ]) ?>
            </div>

            <div class="col-md-2" id="search-quote-rank-slider-filter">
                <div class="form-group">
                    <div class="d-flex align-items-center justify-content-between">
                        <label for="" class="control-label">Rank</label>
                        <span id="search-quote-current-rank-value"></span>
                        <?= $form->field($searchFrom, 'rank')->hiddenInput()->label(false) ?>
                    </div>
                    <div class="d-flex justify-content-center align-items-center" style="width: 100%; height: 100%;">
                        <div class="search-filters__slider" id="search-quote-rank-slider" data-min="0" data-max="10"></div>
                    </div>
                </div>
                <?php $ranks = explode('-', $searchFrom->rank) ?>

                <script>
                    var sliderRank = document.getElementById('search-quote-rank-slider');

                    var maxRank = parseInt(sliderRank.getAttribute('data-max'), 10),
                        minRank = parseInt(sliderRank.getAttribute('data-min'), 10),
                        stepRank = 1;

                    noUiSlider.create(sliderRank, {
                        start: ['<?= $ranks[0] ?>', '<?= $ranks[1] ?>'],
                        connect: [false, true, false],
                        tooltips: [
                            {to: function(value) {return parseInt(value, 10)}},
                            {to: function(value) {return parseInt(value, 10)}}
                        ],
                        step: stepRank,
                        range: {
                            'min': minRank,
                            'max': maxRank
                        }
                    });

                    sliderRank.noUiSlider.on('update', function (values, handle) {
                        $('#search-quote-current-rank-value').html(parseInt(values[0], 10) + ' - ' + parseInt(values[1], 10));
                    });

                    sliderRank.noUiSlider.on('end', function(values) {
                        $('#flightquotesearchform-rank').val(parseInt(values[0], 10) + '-' + parseInt(values[1], 10));
                    });
                </script>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 d-flex align-items-center justify-content-center">
                <?= Html::submitButton('<i class="fa fa-filter"></i> Apply filter', [
                    'class' => 'btn btn-success',
                    'id' => 'quote-search-submit'
                ]) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>


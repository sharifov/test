<?php

use common\models\Quote;
use common\models\Airports;
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
 * @var $lead \common\models\Lead
 */
?>
<div class="x_panel">
    <div class="x_title" style="border: none;">
        <h2><i class="fa fa-filter"></i> Filter Quotes</h2>
        <ul class="nav navbar-right panel_toolbox">
            <li>
                <a id="quote-search-filter-show-hide" class="collapse-link"><i class="fa fa-chevron-down"></i></a>
            </li>
        </ul>
    </div>
    <div class="x_content" id="quote-search-filters" style="display: none; ">
        <div class="row">
            <div class="col-md-12">
                <?php $form = ActiveForm::begin([
                        'options' => [
                            'data-pjax' => 1,
                        ],
                ]) ?>

                <div class="row">
                    <div class="col-md-3">
                        <?= $form->field($searchFrom, 'fareType')->widget(Select2::class, [
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
                        <?= $form->field($searchFrom, 'airlines')->widget(Select2::class, [
                            'options' => [
                                'placeholder' => $searchFrom->getAttributeLabel('airlines'),
                                'multiple' => true,
                                'id' => 'search-quote-airlines'
                            ],
                            'data' => $airlines,
                            'size' => Select2::SIZE_SMALL
                        ]) ?>
                    </div>

                    <div class="col-md-6">
                        <?php /*$form->field($searchFrom, 'price', [
                            'labelOptions' => [
                                'class' => 'control-label'
                            ]
                        ])->input('number', [
                            'max' => $maxPrice,
                            'min' => $minPrice,
                            'autocomplete' => false,
                            'step' => 0.01
                        ])->label('Max Price') */ ?>

                      <div class="form-group">
                        <div class="d-flex align-items-center justify-content-between">
                          <label for="">Max Price</label>
                          <span id="search-quote-price-value"></span>
                        </div>
                        <div class="d-flex justify-content-center align-items-center" style="width: 100%; height: 100%;">
                          <div class="search-filters__slider" id="search-quote-price-filter"></div>
                        </div>
                          <?= $form->field($searchFrom, 'price')->hiddenInput()->label(false) ?>
                        <script>
                            var min = <?= $minPrice ?? 0 ?>;
                            var max = <?= $maxPrice ?? 0 ?>;

                            var start = '<?= $searchFrom->price ?>' || max;
                            var sliderDuration = $('#search-quote-price-filter')[0];
                            noUiSlider.create(sliderDuration, {
                                start: [start],
                                connect: [true, false],
                                tooltips: {
                                    to: function(value){ return Number(value).toFixed(2);}
                                },
                                step: 0.01,
                                range: {
                                    'min': min,
                                    'max': max
                                }
                            });

                            sliderDuration.noUiSlider.on('update', function (values, handle) {
                                $('#search-quote-price-filter').closest('.form-group').find('input').val(values[handle]);
                                $('#search-quote-price-filter').closest('.form-group').find('#search-quote-price-value').html('$'+values[handle]);
                            });
                        </script>
                      </div>
                    </div>

                    <div class="col-md-2">
                        <?= $form->field($searchFrom, 'stops')->dropDownList(Quote::getStopsLIst(), [
                                         'prompt' => '--'
                        ]) ?>
                    </div>

                    <div class="col-md-2">
                        <?= $form->field($searchFrom, 'airportChange')->dropDownList(Quote::getChangeAirportList()) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($searchFrom, 'baggage')->dropDownList(Quote::getBaggageList()) ?>
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-3" id="search-quote-rank-slider-filter">
                        <div class="form-group">
                            <div class="d-flex align-items-center justify-content-between">
                                <label for="">Rank</label>
                                <span id="search-quote-current-rank-value"></span>
                                <?= $form->field($searchFrom, 'rank')->hiddenInput()->label(false) ?>
                            </div>
                            <div class="d-flex justify-content-center align-items-center" style="width: 100%; height: 100%;">
                                <div class="search-filters__slider" id="search-quote-rank-slider" data-min="0.0" data-max="10.0"></div>
                            </div>
                        </div>
                        <?php $ranks = explode('-', $searchFrom->rank) ?>

                        <script>
                            var sliderRank = document.getElementById('search-quote-rank-slider');

                            var maxRank = sliderRank.getAttribute('data-max'),
                                minRank = sliderRank.getAttribute('data-min'),
                                stepRank = 0.1;

                            noUiSlider.create(sliderRank, {
                                start: ['<?= $ranks[0] ?>', '<?= $ranks[1] ?>'],
                                connect: [false, true, false],
                                tooltips: [
                                    {to: function(value) {return value.toFixed(1)}},
                                    {to: function(value) {return value.toFixed(1)}}
                                ],
                                step: stepRank,
                                range: {
                                    'min': Number(minRank),
                                    'max': Number(maxRank)
                                }
                            });

                            sliderRank.noUiSlider.on('update', function (values, handle) {
                                $('#search-quote-current-rank-value').html(Number(values[0]).toFixed(1) + ' - ' + Number(values[1]).toFixed(1));
                            });

                            sliderRank.noUiSlider.on('end', function(values) {
                                $('#flightquotesearchform-rank').val(Number(values[0]).toFixed(1) + '-' + Number(values[1]).toFixed(1));
                            });
                        </script>
                    </div>

                    <div class="col-md-5">
                        <?= $form->field($searchFrom, 'excludeConnectionAirports')->widget(Select2::class, [
                            'options' => [
                                'placeholder' => 'Exclude Connection airports',
                                'multiple' => true,
                                'id' => 'search-quote-exclude-connection-airports'
                            ],
                            'data' => $connectionAirports,
                            'size' => Select2::SIZE_SMALL
                        ]) ?>
                    </div>

                    <div class="col-md-2">
                        <?= $form->field($searchFrom, 'topCriteria')->widget(Select2::class, [
                            'options' => [
                                'placeholder' => '--', #$searchFrom->getAttributeLabel('fareType'),
                                'multiple' => true,
                                'id' => 'fareType'
                                ],
                            'data' => QuoteHelper::TOP_META_LIST,
                            'size' => Select2::SIZE_SMALL
                        ]) ?>
                    </div>

                    <div class="col-md-2">
                        <?= $form->field($searchFrom, 'sortBy')->dropDownList(Quote::getSortList(), ['prompt' => '--']) ?>
                    </div>

                </div>

                <?php foreach ($lead->leadFlightSegments as $key => $segment) : ?>
                <div class="row">
                    <div class="col-md-2">
                        <p style="padding-top: 24px;"><?= $key + 1 ?>. <?= Airports::findByIata($segment->origin)->cityName . ' ' . $segment->origin . ' - ' . Airports::findByIata($segment->destination)->cityName . ' ' . $segment->destination ?></p>
                    </div>
                    <?php if (isset($tripsMinDurationsInMinutes[$key]) && isset($tripsMaxDurationsInMinutes[$key]) && $tripsMinDurationsInMinutes[$key] > 0 && $tripsMaxDurationsInMinutes[$key] > 0) { ?>
                    <div class="col-md-3">
                        <div class="form-group">
                            <div class="d-flex align-items-center justify-content-between">
                                <label for="">Max duration</label>
                            </div>
                            <div class="d-flex align-items-left" style="vertical-align: bottom;">
                                <div>
                                <?php
                                $rangeHoursArray = range(floor($tripsMinDurationsInMinutes[$key] / 60), $tripMaxDurationRoundHours[$key]);
                                $rangeMinutesArray = range(0, 50, 10);
                                ?>
                                <?= $form->field($searchFrom, 'tripMaxDurationHours[' . $key . ']')->dropDownList(array_combine($rangeHoursArray, $rangeHoursArray), ['options' => [$searchFrom->tripMaxDurationHours[$key] ?? $tripMaxDurationRoundHours[$key] => ['Selected' => 'selected']], 'style' => 'width:55px; float: left; margin-right: 7px;'])->label('hours');
                                ?>
                                </div>
                                <?= $form->field($searchFrom, 'tripMaxDurationMinutes[' . $key . ']')->dropDownList(array_combine($rangeMinutesArray, $rangeMinutesArray), ['options' => [$searchFrom->tripMaxDurationMinutes[$key] ?? $tripMaxDurationRoundMinutes[$key] => ['Selected' => 'selected']], 'style' => 'width:55px; float: left; margin-right: 7px; margin-left: 10px;'])->label('minutes') ?>
                            </div>
                            <div>
                                <small>allowable: <?= floor($tripsMinDurationsInMinutes[$key] / 60) . ' h ' . $tripsMinDurationsInMinutes[$key] % 60 . ' m -' . floor($tripsMaxDurationsInMinutes[$key] / 60) . ' h ' . $tripsMaxDurationsInMinutes[$key] % 60 . ' m'?></small>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="col-md-3">
                        <div class="form-group">
                            <div class="d-flex align-items-center justify-content-between">
                                <label for="">Departure</label>
                            </div>
                            <div class="d-flex align-items-left" style="vertical-align: bottom;">
                                <?= $form->field($searchFrom, 'departureStartTimeList[' . $key . ']')->textInput(['type' => 'time', 'style' => 'width: 110px; float: left; margin: 10px;', 'pattern' => '[0-9]{2}:[0-9]{2}'])->label(false) ?> - <?= $form->field($searchFrom, 'departureEndTimeList[' . $key . ']')->textInput(['type' => 'time', 'style' => 'width: 110px; float: left; margin: 10px;', 'pattern' => '[0-9]{2}:[0-9]{2}'])->label(false) ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                          <div class="form-group">
                              <div class="d-flex align-items-center justify-content-between">
                                  <label for="">Arrival</label>
                              </div>
                              <div class="d-flex align-items-left" style="vertical-align: bottom;">
                            <?= $form->field($searchFrom, 'arrivalStartTimeList[' . $key . ']')->textInput(['type' => 'time', 'style' => 'width: 110px; float: left; margin: 10px;', 'pattern' => '[0-9]{2}:[0-9]{2}'])->label(false) ?> - <?= $form->field($searchFrom, 'arrivalEndTimeList[' . $key . ']')->textInput(['type' => 'time', 'style' => 'width: 110px; float: left; margin: 10px;', 'pattern' => '[0-9]{2}:[0-9]{2}'])->label(false) ?>
                              </div>
                          </div>
                    </div>
                    <div class="col-md-1">
                        <?= $form->field($searchFrom, 'excludeNearbyAirports[' . $key . ']')->checkbox(['id' => $segment->id])?>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="row d-flex">
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
    </div>
</div>
<br>

<script>
    var sliderRank = document.getElementById('search-quote-rank-slider');

    var maxRank = sliderRank.getAttribute('data-max'),
        minRank = sliderRank.getAttribute('data-min'),
        stepRank = 0.1;

    noUiSlider.create(sliderRank, {
        start: ['<?= $ranks[0] ?>', '<?= $ranks[1] ?>'],
        connect: [false, true, false],
        tooltips: [
            {to: function(value) {return value.toFixed(1)}},
            {to: function(value) {return value.toFixed(1)}}
        ],
        step: stepRank,
        range: {
            'min': Number(minRank),
            'max': Number(maxRank)
        }
    });

    sliderRank.noUiSlider.on('update', function (values, handle) {
        $('#search-quote-current-rank-value').html(Number(values[0]).toFixed(1) + ' - ' + Number(values[1]).toFixed(1));
    });

    sliderRank.noUiSlider.on('end', function(values) {
        $('#flightquotesearchform-rank').val(Number(values[0]).toFixed(1) + '-' + Number(values[1]).toFixed(1));
    });
</script>


<?php
$css = <<<CSS
    .noUi-connect {
        background: #ccc;
    }
CSS;
$this->registerCss($css);

$js = <<<JS

$(document).on('pjax:success', function() {
    if (localStorage.getItem("quoteSearchFilterIsVisible") === null) {
        localStorage.setItem("quoteSearchFilterIsVisible", '0');
    }
    if (localStorage.getItem("quoteSearchFilterIsVisible") == '0') {
        $('#quote-search-filters').hide();
    } else {
        $('#quote-search-show-filter').hide();
        $('#quote-search-filters').show();
    }
});

JS;
$this->registerJs($js);
?>
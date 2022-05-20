<?php

use common\models\Airports;
use common\models\Currency;
use common\models\Quote;
use frontend\helpers\QuoteHelper;
use kartik\select2\Select2;
use src\forms\api\searchQuote\FlightQuoteSearchForm;
use src\services\CurrencyHelper;
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

$clientCurrencySymbol = CurrencyHelper::getSymbolByCode($lead->leadPreferences->pref_currency ?? Currency::getDefaultCurrencyCode());
?>
<div class="x_panel">
    <div class="x_title">
        <h2><i class="fa fa-filter"></i> Filter data</h2>
        <ul class="nav navbar-right panel_toolbox">
            <li>
                <a id="quote-search-filter-show-hide" class="collapse-link"><i class="fa fa-chevron-<?php if ($searchFrom->filterIsShown) {
                                                                                                        echo 'down';
                                                                                                    } else {
                                                                                                        echo 'up';
                                                                                                    } ?> "></i></a>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" id="quote-search-filters"
        <?php if ($searchFrom->filterIsShown) {
            echo 'style="display: none;"';
        } ?> >
        <div class="row">
            <div class="col-md-12">
                <?php $form = \yii\widgets\ActiveForm::begin([
                        'options' => [
                            'data-pjax' => 1,
                            'id' => 'quote-search-filters-form',
                        ],
                ]) ?>

                <div class="row">

                    <div class="col-md-4">
                        <?= $form->field($searchFrom, 'fareType')->widget(Select2::class, [
                            'options' => [
                                'placeholder' => '--', //$searchFrom->getAttributeLabel('fareType'),
                                'multiple' => true,
                                'id' => 'search-quote-fare-type',
                            ],
                            'data' => Quote::getFareTypeList(),
                            'size' => Select2::SIZE_SMALL
                        ]) ?>
                    </div>

                    <div class="col-md-4">
                        <?= $form->field($searchFrom, 'excludeConnectionAirports')->widget(Select2::class, [
                            'options' => [
                                'placeholder' => '--',
                                'multiple' => true,
                                'id' => 'search-quote-exclude-connection-airports'
                            ],
                            'data' => $connectionAirports,
                            'size' => Select2::SIZE_SMALL
                        ]) ?>
                    </div>

                    <div class="col-md-4">
                        <?= $form->field($searchFrom, 'includeAirports')->widget(Select2::class, [
                            'options' => [
                                'placeholder' => '--',
                                'multiple' => true,
                                'id' => 'search-quote-include-airports'
                            ],
                            'data' => $connectionAirports,
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

                            var currencySymbol = '<?php echo($clientCurrencySymbol);?>';

                            var start = '<?= $searchFrom->price ?>' || max;
                            var sliderDuration = $('#search-quote-price-filter')[0];
                            noUiSlider.create(sliderDuration, {
                                start: [start],
                                connect: [true, false],
                                tooltips: {
                                    to: function(value){ return Math.ceil(value);}
                                },
                                step: 1,
                                range: {
                                    'min': min,
                                    'max': max
                                }
                            });

                            sliderDuration.noUiSlider.on('update', function (values, handle) {
                                $('#search-quote-price-filter').closest('.form-group').find('input').val(Math.ceil(values[handle]));
                                $('#search-quote-price-filter').closest('.form-group').find('#search-quote-price-value').html(currencySymbol+' '+Math.ceil(values[handle]));
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
                        <?= $form->field($searchFrom, 'airlines')->widget(Select2::class, [
                            'options' => [
                                'placeholder' => '--', //$searchFrom->getAttributeLabel('airlines'),
                                'multiple' => true,
                                'id' => 'search-quote-airlines'
                            ],
                            'data' => $airlines,
                            'size' => Select2::SIZE_SMALL
                        ]) ?>
                    </div>

                    <div class="col-md-2">
                        <?= $form->field($searchFrom, 'topCriteria')->widget(Select2::class, [
                            'options' => [
                                'placeholder' => '--', #$searchFrom->getAttributeLabel('fareType'),
                                'multiple' => true,
                                'id' => 'topCriteria'
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
                <div class="quote pl-3">
                    <div class="row mt-2 mb-2 ml-1 font-weight-bold">
                    Trip <?= $key + 1 ?>. <?php
                        $airport = Airports::findByIata($segment->origin);
                        echo $segment->origin . ($airport ? ' (' . $airport->cityName . ') - '  : '');
                        $airport = Airports::findByIata($segment->destination);
                        echo $segment->destination . ($airport ? ' (' . $airport->cityName . ')'  : ''); ?>
                    </div>
                    <div class="row">
                        <?php if (isset($tripsMinDurationsInMinutes[$key]) && isset($tripsMaxDurationsInMinutes[$key]) && $tripsMinDurationsInMinutes[$key] > 0 && $tripsMaxDurationsInMinutes[$key] > 0) : ?>
                            <div class="col-md-3">
                                <div class="">
                                    <label for="" class="mb-0">Max duration</label>
                                    <small>(up to <?= floor($tripsMaxDurationsInMinutes[$key] / 60) . ':' . $tripsMaxDurationsInMinutes[$key] % 60 ?>)</small>

                                </div>
                                <div class="d-flex">
                                    <?php
                                    $rangeHoursArray = range(floor($tripsMinDurationsInMinutes[$key] / 60), $tripMaxDurationRoundHours[$key]);
                                    $rangeMinutesArray = range(0, 50, 10);
                                    ?>
                                    <?= $form->field($searchFrom, 'tripMaxDurationHours[' . $key . ']')->dropDownList(array_combine($rangeHoursArray, $rangeHoursArray), [/*'options' => [$searchFrom->tripMaxDurationHours[$key] ?? $tripMaxDurationRoundHours[$key] => ['Selected' => 'selected']],*/ 'prompt' => '--', 'style' => 'width:55px; margin-right: 5px;'])->label(false); ?>
                                    <span class="font-weight-bold mt-1">:</span>
                                    <?= $form->field($searchFrom, 'tripMaxDurationMinutes[' . $key . ']')->dropDownList(array_combine($rangeMinutesArray, $rangeMinutesArray), [/*'options' => [$searchFrom->tripMaxDurationMinutes[$key] ?? $tripMaxDurationRoundMinutes[$key] => ['Selected' => 'selected']],*/ 'prompt' => '--', 'style' => 'width:55px; margin: 0 5px;'])->label(false) ?>
                                </div>
                            </div>
                        <?php endif ?>
                        <div class="col-md-3">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label class="mb-0" for="">Departure From</label>
                                    <?= $form->field($searchFrom, 'departureStartTimeList[' . $key . ']')->textInput(['type' => 'time', 'pattern' => '[0-9]{2}:[0-9]{2}', 'list' => 'predefinedTimeList'])->label(false) ?>
                                </div>
                                <div class="col-sm-6">
                                    <label class="mb-0" for="">Departure To</label>
                                    <?= $form->field($searchFrom, 'departureEndTimeList[' . $key . ']')->textInput(['type' => 'time', 'pattern' => '[0-9]{2}:[0-9]{2}', 'list' => 'predefinedTimeList'])->label(false) ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-3">
                            <div class="row">
                                <div class="col-sm-6">
                                     <label class="mb-0" for="">Arrival From</label>
                                    <?= $form->field($searchFrom, 'arrivalStartTimeList[' . $key . ']')->textInput(['type' => 'time', 'pattern' => '[0-9]{2}:[0-9]{2}', 'list' => 'predefinedTimeList'])->label(false) ?>
                                </div>
                                <div class="col-sm-6">
                                    <label class="mb-0" for="">Arrival To</label>
                                    <?= $form->field($searchFrom, 'arrivalEndTimeList[' . $key . ']')->textInput(['type' => 'time', 'pattern' => '[0-9]{2}:[0-9]{2}', 'list' => 'predefinedTimeList'])->label(false) ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 mt-4">
                            <?= $form->field($searchFrom, 'airportExactMatch[' . $key . ']')->checkbox(['id' => $segment->id])?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="row d-flex mt-2 justify-content-center">
                        <?= Html::submitButton('<i class="fa fa-check"></i> Apply filter', [
                            'class' => 'btn btn-success',
                            'id' => 'quote-search-submit'
                        ]) ?>
                        <?= Html::button('<i class="fas fa-redo-alt"></i> Reset', [
                            'class' => 'btn btn-warning',
                            'id' => 'quote-search-reset'
                        ]) ?>
                </div>
                <?= $form->field($searchFrom, 'filterIsShown')->hiddenInput(['id' => 'filterIsShown', 'value' => $searchFrom->filterIsShown])->label(false) ?>

                <?php \yii\widgets\ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<br>
    <datalist id="predefinedTimeList">
<?php for ($i = 8; $i < 23; $i++) {
    echo '<option value="' . sprintf('%02d', $i) . ':00">';
}
?>
    </datalist>
<?php
$css = <<<CSS
    .noUi-connect {
        background: #ccc;
    }
CSS;
$this->registerCss($css);

$js = <<<JS

    $(document).on('click', '#quote-search-reset', function(e) {
        $('#quote-search-filters-form :input').not(':button, :submit, :reset, :hidden').val('').prop('checked', false).prop('selected', false);
        $("#quote-search-filters-form select").val(null).trigger("change");
        sliderDuration.noUiSlider.set(sliderDuration.noUiSlider.options.range.max);     // TODO:: change to universal sliders reset (not using slider id) if possible 
        $('#flightquotesearchform-rank').val(sliderRank.noUiSlider.options.range.min + '-' + sliderRank.noUiSlider.options.range.max);
        sliderRank.noUiSlider.set([sliderRank.noUiSlider.options.range.min, sliderRank.noUiSlider.options.range.max]);
    });

JS;
$this->registerJs($js);
?>
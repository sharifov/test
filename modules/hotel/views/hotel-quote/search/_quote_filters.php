<?php

use yii\widgets\ActiveForm;
use yii\bootstrap4\Html;

/**
 * @var $filtersForm \modules\hotel\src\useCases\api\searchQuote\HotelQuoteSearchForm
 */
?>

<?php $form = ActiveForm::begin([
    'options' => [
        'data-pjax' => 1
    ],
]) ?>

<div class="row">
    <div class="col-md-12 d-flex align-items-center justify-content-center">
        <br>
        <?= Html::submitButton('<i class="fa fa-filter"></i> Apply filter', [
            'class' => 'btn btn-success btn-sm',
            //'id' => 'flight-quote-search-submit'
            'id' => 'hotel-quote-search-submit'
        ]) ?>
    </div>
</div>

<?= $form->field($filtersForm, 'onlyHotels')->checkbox() ?>


<div class="form-group">
    <div class="d-flex align-items-center justify-content-between">
        <label for="" class="control-label">Price</label>
        <span id="current-range-value"></span>
        <?= $form->field($filtersForm, 'priceRange')->hiddenInput()->label(false) ?>
        <?= $form->field($filtersForm, 'min')->hiddenInput(['value' => $filtersForm->min])->label(false) ?>
        <?= $form->field($filtersForm, 'max')->hiddenInput(['value' => $filtersForm->max])->label(false) ?>
        <?php $price = explode('-', $filtersForm->priceRange) ?>
    </div>
    <div class="d-flex justify-content-center align-items-center" style="width: 100%; height: 100%;">
        <div class="search-filters__slider" id="price-slider" data-min="<?= $filtersForm->min ?>" data-max="<?= $filtersForm->max ?>"></div>
    </div>
</div>

<script>
    var sliderPrice = document.getElementById('price-slider');

    var maxPrice = parseInt(sliderPrice.getAttribute('data-max'), 10),
        minPrice = parseInt(sliderPrice.getAttribute('data-min'), 10),
        step = 1;

    noUiSlider.create(sliderPrice, {
        start: ['<?= $price[0] ?>', '<?= $price[1] ?>'],
        connect: [false, true, false],
        tooltips: [
            {
                to: function (value) {
                    return parseInt(value, 10)
                }
            },
            {
                to: function (value) {
                    return parseInt(value, 10)
                }
            }
        ],
        step: step,
        range: {
            'min': minPrice,
            'max': maxPrice
        }
    });

    sliderPrice.noUiSlider.on('update', function (values, handle) {
        $('#current-range-value').html('$ ' + parseInt(values[0], 10) + ' - ' + '$ ' + parseInt(values[1], 10));
    });

    sliderPrice.noUiSlider.on('end', function (values) {
        $('#hotelquotesearchform-pricerange').val(parseInt(values[0], 10) + '-' + parseInt(values[1], 10));
    });
</script>


<?php ActiveForm::end(); ?>

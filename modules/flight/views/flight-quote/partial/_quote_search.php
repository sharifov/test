<?php

use modules\flight\models\Flight;
use modules\flight\src\useCases\api\searchQuote\FlightQuoteSearchForm;
use yii\bootstrap4\Alert;
use yii\data\ArrayDataProvider;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * @var $this View
 * @var $quotes []
 * @var $airlines []
 * @var $locations []
 * @var $flightId int
 * @var $gds string
 * @var $errorMessage string
 * @var $flight Flight
 * @var $dataProvider ArrayDataProvider
 * @var $searchForm FlightQuoteSearchForm
 */

if($quotes && (isset($quotes['count']) && $quotes['count'] > 0)):
	$js = <<<JS
    $(document).on('click','.search_details__btn', function (e) {
        e.preventDefault();
        let modal = $('#flight-details__modal');
        $('#flight-details__modal-label').html($(this).data('title'));
        let target = $($(this).data('target')).html();
        modal.find('.modal-body').html(target);
        modal.css('z-index', '1052');
        modal.modal('show');
        $('.modal-backdrop.show').last().css('z-index', '1051');
    });

// init listeners
        

    // $(document).on('change', '#sort_search', function(e) {
    //     var self = $(this).find('option:selected')[0];
    //     $('.search-results__wrapper').html(
    //         $('.search-result__quote').toArray().sort(function(a,b){
    //             var a = +a.getAttribute('data-'+self.getAttribute('data-field'));
    //             var b = +b.getAttribute('data-'+self.getAttribute('data-field'));
    //             if(self.getAttribute('data-sort') === 'asc'){
    //                 return a - b;
    //             }
    //             return b - a;
    //         })
    //     )
    // });

    // var searchResult = new SearchResult();    
    // searchResult.init();

JS;
	$this->registerJs($js);
	?>
    <script>
        pjaxOffFormSubmit('#pjax-quote-filter');
    </script>
    <?php Pjax::begin(['timeout' => 20000, 'enablePushState' => false, 'enableReplaceState' => false, 'scrollTo' => false, 'id' => 'pjax-quote-filter']); ?>
        <?= $this->render('_quote_filters', [
            'minPrice' => $quotes['minPrice'],
            'maxPrice' => $quotes['maxPrice'],
            'airlines' => $airlines,
            'flight' => $flight,
            'searchFrom' => $searchForm,
            'minTotalDuration' => min($quotes['totalDuration']),
            'maxTotalDuration' => max($quotes['totalDuration'])
        ]) ?>

        <div class="search-results__wrapper">
            <?php $n = 0; ?>
                <?= \yii\widgets\ListView::widget([
                    'dataProvider' => $dataProvider,
                    'emptyText' => '<div class="text-center">Not found quotes</div><br>',
                    'itemView' => function ($resultItem, $key, $index, $widget) use ($locations, $airlines) {
                        return $this->render('_quote_search_item', ['resultKey' => $key,'result' => $resultItem,'locations' => $locations,'airlines' => $airlines]);
                    },
                    'layout' => '{summary}{pager}{items}{pager}',
                    'itemOptions' => [
                        'tag' => false,
                    ]
                ]) ?>
	    </div>
    <?php Pjax::end(); ?>



<?php
$js = <<<JS
    $(document).on('click', '.js-filter .dropdown-menu', function(e) {
        if (!$(e.target).hasClass("js-dropdown-close")) {
            // e.preventDefault();
            e.stopPropagation();
        }
    });
    $('.quote__heading [data-toggle="tooltip"]').tooltip();
    
    $("#pjax-quote-filter").on("pjax:beforeSend", function() {
        $('#pjax-quote-filter #flight-quote-search-submit i').removeClass('fa-filter').addClass('fa-spin fa-spinner disabled').prop('disabled', true);
        $('.search-results__wrapper').html('');
    });

    $("#pjax-quote-filter").on("pjax:complete", function() {
        $('#pjax-quote-filter #flight-quote-search-submit i').removeClass('fa-spin fa-spinner disabled').addClass('fa-filter').removeAttr('disabled');
    });

    // $("#pjax-quote-filter").on('pjax:timeout', function(event) {
    // $('#pjax-quote-filter .info-number i').removeClass('fa-spin fa-spinner').addClass('fa-filter');
    // event.preventDefault()
    // });
JS;
$this->registerJs($js);
?>
<?php else:?>
	<div class="search-results__wrapper">
		<?php if (!empty($errorMessage)): ?>
            <div class="row">
                <div class="col-md-12">
					<?= Alert::widget([
						'options' => [
							'class' => 'alert-error',
						],
						'body' => $errorMessage,
					]) ?>
                </div>
            </div>
		<?php endif; ?>
		<p>No search results</p>
	</div>
<?php endif;?>
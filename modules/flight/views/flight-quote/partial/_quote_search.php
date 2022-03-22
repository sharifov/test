<?php

use modules\flight\models\Flight;
use modules\flight\src\useCases\api\searchQuote\FlightQuoteSearchForm;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use src\auth\Auth;
use yii\bootstrap4\Alert;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\VarDumper;
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
 * @var $pjaxId string
 * @var $flightQuoteSearchDTO \modules\flight\src\dto\flightQuoteSearchDTO\FlightQuoteSearchDTO
 */

$leadAbacDto = new LeadAbacDto($flight->flProduct->prLead ?? null, Auth::id());
$canDisplayQuoteSearchParams = Yii::$app->abac->can($leadAbacDto, LeadAbacObject::UI_DISPLAY_QUOTE_SEARCH_PARAMS, LeadAbacObject::ACTION_ACCESS);

if ($quotes && (isset($quotes['count']) && $quotes['count'] > 0)) :
    $js = <<<JS
    $(document).on('click','.search_details__btn', function (e) {
        e.preventDefault();
        let modal = $('#flight-details__modal');
        $('#flight-details__modal-label').html($(this).data('title'));
        let target = $($(this).data('target')).html();
        modal.find('.modal-body').html(target);
        modal.css('z-index', '1052');
        modal.modal('show');
        $('.modal-backdrop.show, #modal-lg').last().css('z-index', '1051');
    });
    
    $('#modal-lg, .flight_quote_ngs_btn').off().on('click', '.flight_quote_ngs_btn', function (e) {
        e.preventDefault(); 
        let target = $(this).attr('data-target');
        let collapseDiv = document.getElementById(target);
        collapseDiv.classList.toggle("show");
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

    $flightQuotes = ArrayHelper::getColumn($flight->flightQuotes, 'fq_hash_key');
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
            <?php $n = 0;
            $layout = '{pager}'; ?>
                <?php if ($canDisplayQuoteSearchParams) : ?>
                    <?php $layout = '<div class="d-flex justify-content-between align-items-center">{pager} <span data-toggle="collapse" href="#collapseSearchParams" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fas fa-info-circle"></i> Search Query Params</span></div>'; ?>
                  <div id="collapseSearchParams" class="collapse">
                    <div class="card-body card">
                      <h4>Search Query Params</h4>
                      <pre><?= Html::encode(VarDumper::dumpAsString($flightQuoteSearchDTO->getAsArray())) ?></pre>
                    </div>
                  </div>
                <?php endif; ?>
                <?= \yii\widgets\ListView::widget([
                    'dataProvider' => $dataProvider,
                    'emptyText' => '<div class="text-center">Not found quotes</div><br>',
                    'itemView' => function ($resultItem, $key, $index, $widget) use ($locations, $airlines, $flightQuotes) {
                        return $this->render('_quote_search_item', ['resultKey' => $key,'result' => $resultItem,'locations' => $locations,'airlines' => $airlines, 'flightQuotes' => $flightQuotes]);
                    },
                    'layout' => '{summary}' . $layout . '{items}{pager}',
                    'itemOptions' => [
                        'tag' => false,
                    ]
                ]) ?>
        </div>
    <?php
    $urlCreateFlightQuoteFromSearch = Url::to(['/flight/flight-quote/ajax-add-quote']);
    $js = <<<JS
    $(document).on('click', '.js-filter .dropdown-menu', function(e) {
        if (!$(e.target).hasClass("js-dropdown-close")) {
            // e.preventDefault();
            e.stopPropagation();
        }
    });
    $('[data-toggle="tooltip"]').tooltip({html:true});
    
    $("#pjax-quote-filter").on("pjax:beforeSend", function() {
        $('#pjax-quote-filter #flight-quote-search-submit i').removeClass('fa-filter').addClass('fa-spin fa-spinner disabled').prop('disabled', true);
        $('.search-results__wrapper').addClass('loading');
    });

    $("#pjax-quote-filter").on("pjax:complete", function() {
        $('#pjax-quote-filter #flight-quote-search-submit i').removeClass('fa-spin fa-spinner disabled').addClass('fa-filter').removeAttr('disabled');
        $('.search-results__wrapper').removeClass('loading');
    });
    
    $('#flight-details__modal, .search-result__quote_wrapper').off().on('click', '.flight_create_quote__btn', function (e) {
        e.preventDefault();
        var key = $(this).data('key');
        var gds = $(this).data('gds');
        var searchResId = $(this).data('result');
        var btn = $(this);
        $('#preloader').removeClass('d-none');
        $.ajax({
            url: '$urlCreateFlightQuoteFromSearch',
            type: 'post',
            data: {'key': key, 'gds': gds, flightId: '$flightId'},
            beforeSend: function () {
              $('#'+searchResId).addClass('loading');
            },
            success: function (data) {
                var error = '';
                
                $('#preloader').addClass('d-none');
                if(data.status == true){
                    //$('#search-results__modal').modal('hide');
                    $('#flight-details__modal').modal('hide');
                    $('#'+searchResId).addClass('quote--selected')
                    let cardFooter = btn.closest('.card-footer');
                    if (cardFooter.length) {
                        btn.html('<p>Quote already assigned</p>'); // todo: is this trash (never executed)?
                    } else {
                        // btn.remove();
                        btn.attr('disabled', true).prop('disabled', true);
                        btn.html('Quote assigned');
                    }
                    

                    // $.pjax.reload({container: '#quotes_list', async: false});
                    $('.popover-class[data-toggle="popover"]').popover({ sanitize: false });
                    
                    createNotifyByObject({
                        title: "Create quote - search",
                        type: "success",
                        text: 'Added new quote',
                        hide: true
                    });
                    
                    $.pjax.reload({container: '#$pjaxId', url: "/flight/flight/pjax-flight-request-view?pr_id=$flight->fl_product_id",  push: false, replace: false, timeout: 2000});
                } else {
                    if(data.error) {
                        error = data.error;    
                    } else {
                        error = 'Some errors was happened during create quote. Please try again later';
                    }
                    
                    createNotifyByObject({
                        title: "Error: Create quote - search",
                        type: "error",
                        text: error,
                        hide: true
                    });
                }
            },
            error: function (error) {
                console.log('Error: ' + error);
            },
            complete: function () {
              $('#'+searchResId).removeClass('loading');
            }
        });
    });
JS;
    $this->registerJs($js);
    ?>
    <?php Pjax::end(); ?>

<?php else :?>
    <div class="search-results__wrapper">
        <?php if (!empty($errorMessage)) : ?>
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
        <?php if ($canDisplayQuoteSearchParams && isset($flightQuoteSearchDTO)) : ?>
          <div class="d-flex justify-content-between align-items-center"><span data-toggle="collapse" href="#collapseSearchParams" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fas fa-info-circle"></i> Search Query Params</span></div>
          <div id="collapseSearchParams" class="collapse">
            <div class="card-body card">
              <h4>Search Query Params</h4>
              <pre><?= Html::encode(VarDumper::dumpAsString($flightQuoteSearchDTO->getAsArray())) ?></pre>
            </div>
          </div>
        <?php endif; ?>
    </div>
<?php endif;?>
<?php

use common\models\Lead;
use modules\lead\src\abac\dto\LeadAbacDto;
use modules\lead\src\abac\LeadAbacObject;
use sales\auth\Auth;
use sales\forms\api\searchQuote\FlightQuoteSearchForm;
use yii\bootstrap4\Alert;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\web\View;
use yii\widgets\ListView;
use yii\widgets\Pjax;

/**
 * @var $this View
 * @var $quotes []
 * @var $airlines []
 * @var $locations []
 * @var $leadId int
 * @var $gds string
 * @var $lead Lead
 * @var $dataProvider ArrayDataProvider
 * @var $searchForm FlightQuoteSearchForm
 * @var $keyCache string
 * @var $searchServiceQuoteDto \sales\dto\searchService\SearchServiceQuoteDTO
 */
//\yii\helpers\VarDumper::dump($airlines);die;

$leadAbacDto = new LeadAbacDto($lead ?? null, Auth::id());
$canDisplayQuoteSearchParams = Yii::$app->abac->can($leadAbacDto, LeadAbacObject::UI_DISPLAY_QUOTE_SEARCH_PARAMS, LeadAbacObject::ACTION_ACCESS);
if ($quotes && (isset($quotes['count']) && $quotes['count'] > 0)) :
    $js = <<<JS
    $(document).on('click','.search_quote_details__btn', function (e) {
        e.preventDefault();
        let modal = $('#modal-md');
        $('#modal-lg-label').html($(this).data('title'));
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

    $flightQuotes = ArrayHelper::getColumn($lead->quotes, 'fq_hash_key');
    ?>
    <script>
        pjaxOffFormSubmit('#pjax-search-quote-filter');
    </script>
    <?php Pjax::begin(['timeout' => 20000, 'enablePushState' => false, 'enableReplaceState' => false, 'scrollTo' => false, 'id' => 'pjax-search-quote-filter']); ?>
    <?= $this->render('_quote_filters', [
    'minPrice' => $quotes['minPrice'],
    'maxPrice' => $quotes['maxPrice'],
    'airlines' => $airlines,
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
              <pre><?= Html::encode(VarDumper::dumpAsString($searchServiceQuoteDto->getAsArray())) ?></pre>
            </div>
          </div>
        <?php endif; ?>
        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'emptyText' => '<div class="text-center">Not found quotes</div><br>',
            'itemView' => function ($resultItem, $key, $index, $widget) use ($locations, $airlines, $flightQuotes, $keyCache, $lead) {
                return $this->render(
                    '_quote_search_item',
                    [
                        'resultKey' => $key,
                        'result' => $resultItem,
                        'locations' => $locations,
                        'airlines' => $airlines,
                        'flightQuotes' => $flightQuotes,
                        'keyCache' => $keyCache,
                        'lead' => $lead,
                    ]
                );
            },
            'layout' => '{summary}' . $layout . '{items}{pager}',
            'itemOptions' => [
                'tag' => false,
            ],
        ]) ?>
    </div>
    <?php
    $urlCreateQuoteFromSearch = Url::to(['quote/create-quote-from-search', 'leadId' => $lead->id]);
    $js = <<<JS
    $(document).on('click', '.js-filter .dropdown-menu', function(e) {
        if (!$(e.target).hasClass("js-dropdown-close")) {
            // e.preventDefault();
            e.stopPropagation();
        }
    });
    $('[data-toggle="tooltip"]').tooltip({html:true});
    
    $("#pjax-search-quote-filter").on("pjax:beforeSend", function() {
        $('#pjax-search-quote-filter #quote-search-submit i').removeClass('fa-filter').addClass('fa-spin fa-spinner disabled').prop('disabled', true);
        $('.search-results__wrapper').addClass('loading');
    });

    $("#pjax-search-quote-filter").on("pjax:complete", function() {
        $('#pjax-search-quote-filter #quote-search-submit i').removeClass('fa-spin fa-spinner disabled').addClass('fa-filter').removeAttr('disabled');
        $('.search-results__wrapper').removeClass('loading');
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

        <?php if ($canDisplayQuoteSearchParams && isset($searchServiceQuoteDto)) : ?>
            <div class="d-flex justify-content-between align-items-center"><span data-toggle="collapse" href="#collapseSearchParams" role="button" aria-expanded="false" aria-controls="collapseExample"><i class="fas fa-info-circle"></i> Search Query Params</span></div>
            <div id="collapseSearchParams" class="collapse">
              <div class="card-body card">
                <h4>Search Query Params</h4>
                <pre><?= Html::encode(VarDumper::dumpAsString($searchServiceQuoteDto->getAsArray())) ?></pre>
              </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif;?>
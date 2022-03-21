<?php

/**
 * @var $this \yii\web\View
 * @var $product Product
 */

use modules\flight\src\helpers\FlightQuoteHelper;
use modules\product\src\entities\product\Product;
use yii\widgets\ListView;
use yii\widgets\Pjax;

$dataProvider = FlightQuoteHelper::generateDataProviderForQuoteList($product);
?>


<?php Pjax::begin(['id' => 'pjax-product-quote-list-' . $product->pr_id, 'timeout' => 2000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
<div class="x_panel">
    <div class="x_title">
        <h2><i class="fa fa-folder-o"></i> Flight Quotes
            <?php if ($dataProvider->totalCount) : ?>
                <sup>(<?=$dataProvider->totalCount?>)</sup>
            <?php endif; ?>
        </h2>
        <ul class="nav navbar-right panel_toolbox">
            <li>
                <?= \yii\bootstrap4\Html::a('<i class="fa fa-search warning"></i> Search Quotes', null, [
                    'data-url' => \yii\helpers\Url::to([
                        '/flight/flight-quote/ajax-search-quote',
                        'id' => $product->flight->fl_id
                    ]),
                    'data-flight-id' => $product->flight->fl_id,
                    'class' => 'btn-search-flight-quotes'
                ]) ?>
            </li>



            <li>
                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block">

        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'itemView' => '_quote_item',
            'emptyText' => '<div class="text-center">Not found quotes</div><br>',
            //'layout' => "\n{items}<div class=\"text-center\">{pager}</div>\n", // {summary}\n<div class="text-center">{pager}</div>
            'viewParams' => [
//                'appliedQuote' => $lead->getAppliedAlternativeQuotes(),
//                'leadId' => $lead->id,
//                'leadForm' => $leadForm,
//                'isManager' => $is_manager,
            ],
            'itemOptions' => [
                //'class' => 'item',
                'tag' => false,
            ],
        ]);?>

    </div>
</div>
<?php Pjax::end() ?>

<?php
$statusLogUrl = \yii\helpers\Url::to(['/flight/flight-quote/quote-status-log']);
// Menu details

$js = <<<JS

    $('body').off('click', '.js-btn-book-flight-quote').on('click', '.js-btn-book-flight-quote', function (e) {

        if(!confirm('Are you sure you want to book flight quote?')) {
            return false;
        }

        e.preventDefault();
        $('#preloader').removeClass('d-none');
        let quoteId = $(this).data('flight-quote-id');
        let productId = $(this).data('product-id');
        
        $.ajax({
          url: $(this).data('url'),
          type: 'post',
          data: {id: quoteId},
          cache: false,
          dataType: 'json',
        }).done(function(data) {
            if (parseInt(data.status) === 1) {
                createNotifyByObject({
                    title: 'The quote was successfully booking',
                    type: 'success',
                    text: data.message,
                    hide: true
                });
                pjaxReload({container: '#pjax-product-quote-list-' + productId});
                addFileToFileStorageList();                
            } else {
                createNotifyByObject({
                    title: 'Booking failed',
                    type: 'error',
                    text: data.message,
                    hide: true
                });
                pjaxReload({
                    container: '#pjax-product-quote-list-' + productId
                });
            }
        })
        .fail(function( jqXHR, textStatus ) {
            alert( "Request failed: " + textStatus );
        }).always(function() {
            $('#preloader').addClass('d-none');
        });
    });
    
    $('body').off('click', '.js-btn-generate-pdf-flight-quote').on('click', '.js-btn-generate-pdf-flight-quote', function (e) {

        if(!confirm('Are you sure you want to generate documents?')) {
            return false;
        }

        e.preventDefault();
        $('#preloader').removeClass('d-none');
        let quoteId = $(this).data('flight-quote-id');
                
        $.ajax({
          url: $(this).data('url'),
          type: 'post',
          data: {id: quoteId},
          cache: false,
          dataType: 'json',
        }).done(function(data) {
            if (parseInt(data.status) === 1) {
                createNotifyByObject({
                    title: 'Document have been successfully generated',
                    type: 'success',
                    text: data.message,
                    hide: true
                });                
                addFileToFileStorageList();                
            } else {
                createNotifyByObject({
                    title: 'File generated failed',
                    type: 'error',
                    text: data.message,
                    hide: true
                });                
            }
        })
        .fail(function( jqXHR, textStatus ) {
            alert( "Request failed: " + textStatus );
        }).always(function() {
            $('#preloader').addClass('d-none');
        });
    });

    $('body').on('click','.btn-flight-quote-details', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        let modal = $('#modal-lg');
        //var modal = $('#modal-info-d');
        $('#modal-lg-label').html($(this).data('title'));
        //modal.find('.modal-header h2').text($(this).data('title'));
        modal.find('.modal-body').html('');
        $('#preloader').removeClass('hidden');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            if (status == 'error') {
                alert(response);
            } else {
                $('#preloader').addClass('hidden');
                modal.modal('show');
            }
        });
    });
JS;
$this->registerJs($js);
?>

<?php
$js = <<<JS
    $(document).on('pjax:end', function() {
        $('[data-toggle="tooltip"]').tooltip({html:true});
    });
    $('[data-toggle="tooltip"]').tooltip({html:true});
JS;
$this->registerJs($js);

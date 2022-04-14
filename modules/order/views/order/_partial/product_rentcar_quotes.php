<?php

use modules\rentCar\src\entity\rentCar\RentCar;
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use yii\data\ArrayDataProvider;

/**
 * @var $data array
 * @var $productId int
 * @var $this yii\web\View
 */

$dataProvider = new ArrayDataProvider([
    'allModels' => array_reverse($data),
    'pagination' => [
        'pageSize' => 1,
    ],
]);
//\yii\helpers\VarDumper::dump($dataProvider->getModels(), 10, true); exit();
$pjaxId = 'pjax-product-quote-list-' . $productId;
?>
    <div class="hotel-view-product-quotes">
        <?php \yii\widgets\Pjax::begin(['id' => $pjaxId, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>

        <div class="x_panel">
            <div class="x_title">
                <h2>
                    <i class="fa fa-folder-o"></i> Rent Car Quotes
                    <?php if ($dataProvider->totalCount) : ?>
                        <sup>(<?=$dataProvider->totalCount?>)</sup>
                    <?php endif; ?>
                </h2>

                <ul class="nav navbar-right panel_toolbox">
                    <!--<li>
                        <?/*= Html::a('<i class="fa fa-search warning"></i> Search Rent Car', null, [
                            'data-url' => \yii\helpers\Url::to([
                                '/rent-car/rent-car-quote/search-ajax',
                                'id' => $productId
                            ]),
                            'data-model-id' => $productId,
                            'class' => 'btn-search-rent-car-quotes'
                        ]) */?>
                    </li>-->
                    <li>
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content" style="display: block">
                <?= ListView::widget([
                    'dataProvider' => $dataProvider,
                    'emptyText' => '<div class="text-center">Not found quotes</div><br>',
                    'itemView' => 'product_rentcar_quote_item',
                    /*'itemView' => function ($model, $key, $index, $widget) use ($rentCar) {
                        return $this->render('_list_product_quote', ['modelQuote' => $model, 'index' => $index, 'key' => $key, 'rentCar' => $rentCar]);
                    },*/
                    'itemOptions' => [
                        'tag' => false,
                    ],
                ]) ?>
            </div>
        </div>

        <?php \yii\widgets\Pjax::end(); ?>
    </div>

<?php
$js = <<<JS
    $('body').off('click', '.js-btn-book-rent-car').on('click', '.js-btn-book-rent-car', function (e) {
        e.preventDefault();
        if(!confirm('Are you sure you want to book this quote?')) {
            return false;
        }
        
        $('#preloader').removeClass('d-none');
        let quoteId = $(this).data('rent-car-quote-id');
        let productId = $(this).data('product-id');
        
        $.ajax({
          url: $(this).data('url'),
          type: 'post',
          data: {'id': quoteId},
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
                //addFileToFileStorageList();                
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
    
    $('body').off('click', '.js-btn-generate-pdf-rent-car').on('click', '.js-btn-generate-pdf-rent-car', function (e) {
        e.preventDefault();
        if(!confirm('Are you sure you want to generate documents?')) {
            return false;
        }
                
        $('#preloader').removeClass('d-none');
        let quoteId = $(this).data('rent-car-quote-id');
                
        $.ajax({
          url: $(this).data('url'),
          type: 'post',
          data: {'id': quoteId},
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
    
    $('body').off('click', '.js-btn-cancel-book-rent-car').on('click', '.js-btn-cancel-book-rent-car', function (e) {
        e.preventDefault();
        if(!confirm('Are you sure you want to cancel book this quote?')) {
            return false;
        }
           
        $('#preloader').removeClass('d-none');
        let quoteId = $(this).data('rent-car-quote-id');
        let productId = $(this).data('product-id');
        
        $.ajax({
          url: $(this).data('url'),
          type: 'post',
          data: {'id': quoteId},
          cache: false,
          dataType: 'json',
        }).done(function(data) {
            if (parseInt(data.status) === 1) {
                createNotifyByObject({
                    title: 'Booking is canceled',
                    type: 'success',
                    text: data.message,
                    hide: true
                });
                pjaxReload({
                    container: '#pjax-product-quote-list-' + productId
                });
            } else {
                createNotifyByObject({
                    title: 'Process failed',
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
JS;

$this->registerJs($js, \yii\web\View::POS_READY);

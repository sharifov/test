<?php

/**
 * @var $this yii\web\View
 * @var $model ProductQuote
 */

use kartik\editable\Editable;
use modules\hotel\models\Hotel;
use modules\hotel\models\HotelQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionStatus;
use src\auth\Auth;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\web\View;
use yii\helpers\Html;
use yii\widgets\Pjax;
use modules\attraction\src\helpers\AttractionQuoteHelper;
use modules\product\src\entities\productQuote\ProductQuote;

?>


    <?php
    $js = <<<JS
    $('body').off('click', '.btn-attraction-book-quote').on('click', '.btn-attraction-book-quote', function (e) {

        if(!confirm('Are you sure you want to book this quote?')) {
            return '';
        }

        e.preventDefault();
        $('#preloader').removeClass('d-none');
        let quoteId = $(this).data('attraction-quote-id');
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
                pjaxReload({
                    container: '#pjax-product-quote-list-' + productId
                });
                 pjaxReload({container: "#pjax-lead-offers"});
                 pjaxReload({container: "#pjax-lead-orders"});
                 /*pjaxReload({container: "#pjax-lead-products-wrap"});*/
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
    
    $('body').off('click', '.btn-attraction-cancel-book-quote').on('click', '.btn-attraction-cancel-book-quote', function (e) {

        if(!confirm('Are you sure you want to cancel book this quote?')) {
            return '';
        }

        e.preventDefault();
        $('#preloader').removeClass('d-none');
        let quoteId = $(this).data('attraction-quote-id');
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
                pjaxReload({container: "#pjax-lead-offers"});
                pjaxReload({container: "#pjax-lead-orders"});
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
    
    $(document).on('click', '.btn-product-api-service-log', function(e){        
        e.preventDefault();
        let url = $(this).data('url');
        let quoteId = $(this).data('attraction-quote-id');
        let modal = $('#modal-lg');
          
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Api service log [' + quoteId + ']');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            //$('#preloader').addClass('d-none');
            modal.modal({
              backdrop: 'static',
              show: true
            });
        });
     });
    
    $('body').off('click', '.js-btn-generate-pdf-attraction-quote').on('click', '.js-btn-generate-pdf-attraction-quote', function (e) {

        e.preventDefault();
        
        if(!confirm('Are you sure you want to generate documents?')) {
            return '';
        }
        
        let quoteId = $(this).data('quote-id');
                
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
    
    $('body').on('click','.btn-attraction-quote-details', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        let modal = $('#modal-lg');       
        $('#modal-lg-label').html($(this).data('title'));        
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

    $this->registerJs($js, \yii\web\View::POS_READY);
    ?>

    <?php Pjax::begin(['id' => 'pjax-product-quote-' . $model->pq_id, 'timeout' => 2000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
    <div class="x_panel">
        <div class="x_title">
            <span class="badge badge-white">Q<?=($model->attractionQuote->atnq_product_quote_id)?></span> Attraction "<b><?=\yii\helpers\Html::encode($model->attractionQuote->atnq_attraction_name)?></b>"

            | <?= ProductQuoteStatus::asFormat($model->pq_status_id) ?>

            <i class="ml-2 fas fa-donate" title="Profit Amount"></i> <?= $model->pq_profit_amount ?>

            <?php if ($model->pq_clone_id) : ?>
                <span class="badge badge-warning" style="padding-left: 5px">CLONE</span>
            <?php endif;?>

            <ul class="nav navbar-right panel_toolbox">
                <!--            <li>-->
                <!--                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>-->
                <!--            </li>-->
                <!--<li class="dropdown dropdown-offer-menu" data-product-quote-id="<?/*=($model->atnq_product_quote_id)*/?>" data-lead-id="<?/*=($attractionProduct->atnProduct->pr_lead_id)*/?>" data-url="<?/*= Url::to(['/offer/offer/list-menu-ajax'])*/?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="far fa-handshake"></i> Offers</a>
                    <div class="dropdown-menu" role="menu">
                        <?php /*// ajax loaded content */?>
                    </div>
                </li>

                <li class="dropdown dropdown-order-menu" data-product-quote-id="<?/*=($model->atnq_product_quote_id)*/?>" data-lead-id="<?/*=($attractionProduct->atnProduct->pr_lead_id)*/?>" data-url="<?/*= Url::to(['/order/order/list-menu-ajax'])*/?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fas fa-money-check-alt"></i> Orders</a>
                    <div class="dropdown-menu" role="menu">
                        <?php /*// ajax loaded content */?>
                    </div>
                </li>-->

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-bars text-warning"></i></a>
                    <div class="dropdown-menu" role="menu">
                        <h6 class="dropdown-header">Quote Q<?=($model->attractionQuote->atnq_product_quote_id)?></h6>

                        <?= Html::a('<i class="fa fa-search"></i> Details', null, [
                            'class' => 'btn-attraction-quote-details dropdown-item',
                            'data-id' => $model->pq_id,
                            'data-title' => '<i class="fas fa-archway"></i> ' . $model->attractionQuote->atnqAttraction->atn_destination,
                            'data-url' => Url::to(['/attraction/attraction-quote/ajax-quote-details', 'id' => $model->pq_id]),
                            'title' => 'Details'
                        ]) ?>

                        <!--<?php /*if ($model->isBookable()) : */?>
                            <?/*= Html::a(
                                '<i class="fa fa-share-square"></i> Book',
                                null,
                                [
                                    'class' => 'dropdown-item btn-attraction-book-quote',
                                    'data-url' => Url::to('/attraction/attraction-quote/ajax-book'),
                                    'data-attraction-quote-id' => $model->atnq_id,
                                    'data-product-id' => $model->atnqProductQuote->pq_product_id,
                                ]
                            ) */?>
                        <?php /*endif; */?>
                        <?php /*if ($model->isBooking()) : */?>
                            <?/*= Html::a(
                                '<i class="fa fa-share-square"></i> Cancel Book',
                                null,
                                [
                                    'class' => 'dropdown-item text-danger btn-attraction-cancel-book-quote',
                                    'data-url' => Url::to('/attraction/attraction-quote/ajax-cancel-book'),
                                    'data-attraction-quote-id' => $model->atnq_id,
                                    'data-product-id' => $model->atnqProductQuote->pq_product_id,
                                ]
                            ) */?>
                        <?php /*endif; */?>

                        <?/*= Html::a('<i class="fa fa-plus-circle"></i> Add option', null, [
                            'class' => 'dropdown-item text-success btn-add-product-quote-option',
                            'data-url' => Url::to(['/product/product-quote-option/create-ajax', 'id' => $model->atnq_product_quote_id]),
                        ]) */?>

                        <?/*= Html::a(
                            '<i class="fa fa-list"></i> API Service Log',
                            null,
                            [
                                'class' => 'dropdown-item text-secondary btn-product-api-service-log',
                                'data-url' => Url::to(['/attraction/hotel-quote-service-log/hotel-quote-log', 'id' => $model->atnq_id]),
                                'data-attraction-quote-id' => $model->atnq_id,
                                'data-product-id' => $model->atnqProductQuote->pq_product_id,
                            ]
                        )*/?>

                        <?/*= Html::a('<i class="fa fa-list"></i> Status log', null, [
                            'class' => 'dropdown-item text-secondary btn-product-quote-status-log',
                            'data-url' => Url::to(['/product/product-quote-status-log/show', 'gid' => $model->atnqProductQuote->pq_gid]),
                            'data-gid' => $model->atnqProductQuote->pq_gid,
                        ]) */?>

                        <?php /*if (Auth::can('/attraction/attraction-quote/ajax-file-generate') && $model->isBooking()) : */?>
                            <?/*= Html::a(
                                '<i class="fa fa-file-pdf-o"></i> Generate PDF',
                                null,
                                [
                                    'class' => 'dropdown-item js-btn-generate-pdf-attraction-quote',
                                    'data-url' => Url::to('/attraction/attraction-quote/ajax-file-generate'),
                                    'data-quote-id' => $model->atnq_id,
                                ]
                            ) */?>
                        <?php /*endif; */?>

                        <div class="dropdown-divider"></div>
                        <?/*= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Delete quote', null, [
                            'class' => 'dropdown-item text-danger btn-delete-product-quote',
                            'data-product-quote-id' => $model->atnq_product_quote_id,
                            'data-attraction-quote-id' => $model->atnq_id,
                            'data-product-id' => $model->atnqProductQuote->pq_product_id,
                        ]) */?>-->
                    </div>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: block">
            <i class="fa fa-user"></i> <?=$model->pqCreatedUser ? Html::encode($model->pqCreatedUser->username) : '-'?>,
            <i class="fa fa-calendar fa-info-circle"></i> <?=Yii::$app->formatter->asDatetime(strtotime($model->pq_created_dt)) ?>
            <!--<i title="code: <?php /*=\yii\helpers\Html::encode($model->atnq_hash_key)*/?>">Hash: <?php /* =\yii\helpers\Html::encode($model->atnq_hash_key)*/?></i>-->


                <?php Pjax::begin(['id' => 'pjax-quote_prices-' . $model->attractionQuote->atnq_id, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
                <?= $this->render('attraction_quote_item_prices', [
                    'attractionQuote' => $model->attractionQuote,
                    'quote' => $model,
                    'priceData' => AttractionQuoteHelper::getPricesData($model->attractionQuote)
                ]); ?>
                <?php Pjax::end(); ?>


            <?= $this->render('@frontend/views/lead/quotes/partial/_quote_option_list', ['productQuote' => $model]) ?>
            <?= $this->render('@frontend/views/lead/quotes/partial/_quote_total', ['productQuote' => $model]) ?>

        </div>
    </div>
    <?php Pjax::end(); ?>



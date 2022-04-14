<?php

/* @var $this yii\web\View */
/* @var $index int */
/* @var $key int */
/* @var $attractionProduct \modules\attraction\models\Attraction */
/* @var $model \modules\attraction\models\AttractionQuote */

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

?>

<?php if ($model->atnqProductQuote) : ?>
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
        let modal = $('#modal-lg');        
        
        $.ajax({
          url: $(this).data('url'),
          type: 'post',
          data: {'id': quoteId},
          cache: false,
          dataType: 'json',
        }).done(function(data) {
            if (parseInt(data.status) === 1) {
                modal.find('.modal-body').html(data.html);
                modal.modal('show');
                /*createNotifyByObject({
                    title: 'The quote was successfully booking',
                    type: 'success',
                    text: data.message,
                    hide: true
                });*/
                pjaxReload({
                    container: '#pjax-product-quote-list-' + productId
                });
                pjaxReload({container: "#pjax-lead-offers"});
                pjaxReload({container: "#pjax-lead-orders"});
                /*pjaxReload({container: "#pjax-lead-products-wrap"});*/
            } else {
                createNotifyByObject({
                    title: 'Booking process failed',
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
    
    $('#bk-answer-form').on('beforeSubmit', function (e) {
        e.preventDefault();
        let modal = $('#modal-lg');        
        
       $.ajax({
       type: $(this).attr('method'),
       url: $(this).attr('action'),
       data: $(this).serializeArray(),
       cache: false,
       dataType: 'json',
       success: function(data) {            
           if (parseInt(data.status) === 1) {
                modal.find('.modal-body').html(data.html);
           }
           
            /*if (!data.error) {
                $.pjax.reload({container: '#pjax-client-info', timeout: 10000, async: false});
                
                let clientLocale = $('#casesclientupdateform-locale').val();
                if (typeof clientLocale !== typeof undefined && clientLocale.length && $('#language option[value=' + clientLocale + ']').length) {
                    $('#language option[value=' + clientLocale + ']').prop('selected', true);
                }
                
                $('#modalCaseSm').modal('hide');
                
                createNotifyByObject({
                    title: 'Client info successfully updated',
                    text: data.message,
                    type: 'success'
                });
            }*/
       },
       error: function (error) {
            createNotifyByObject({
                title: 'Error',
                text: 'Internal Server Error. Try again letter.',
                type: 'error'                
            });
       }
    })
    return false;
    });
    
    $('body').off('click', '.js-btn-booking-confirmation').on('click', '.js-btn-booking-confirmation', function (e) {
        e.preventDefault();
        
        if(!confirm('Are you sure you want to check booking confirmation ?')) {
            return '';
        }
        
        let bookId = $(this).data('book-id');
        let modal = $('#modal-lg');
                
        $.ajax({
          url: $(this).data('url'),
          type: 'post',
          data: {'id': bookId},
          cache: false,
          dataType: 'json',
        }).done(function(data) {
            if (parseInt(data.status) === 1) {
                modal.find('.modal-title').html('Check Booking Status');
                modal.find('.modal-body').html(data.html);
                if (data.productID) {
                    pjaxReload({
                        container: '#pjax-product-quote-list-' + data.productID
                    });
                }
                
                createNotifyByObject({
                    title: 'Booking Info',
                    type: 'success',
                    text: data.message,
                    hide: true
                });               
                              
            } else {
                createNotifyByObject({
                    title: 'Please check later',
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
    
    $('body').off('click', '.js-btn-attraction-booking-state').on('click', '.js-btn-attraction-booking-state', function (e) {
        e.preventDefault();
        
        if(!confirm('Are you sure you want to review booking state ?')) {
            return '';
        }
        
        let quoteId = $(this).data('quote-id');
        let modal = $('#modal-lg');
                
        $.ajax({
          url: $(this).data('url'),
          type: 'post',
          data: {'id': quoteId},
          cache: false,
          dataType: 'json',
        }).done(function(data) {
            if (parseInt(data.status) === 1) {
                modal.find('.modal-title').html(data.productID ? 'Answer for booking questions' : 'Check Booking Status');
                modal.find('.modal-body').html(data.html);
                modal.modal('show');
                if (data.productID) {
                    pjaxReload({
                        container: '#pjax-product-quote-list-' + data.productID
                    });
                }           
            } else {
                createNotifyByObject({
                    title: 'Please check later',
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
    
JS;

    $this->registerJs($js, \yii\web\View::POS_READY);
    ?>

    <?php Pjax::begin(['id' => 'pjax-product-quote-' . $model->atnqProductQuote->pq_id, 'timeout' => 2000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
<div class="x_panel">
    <div class="x_title">
        <span class="badge badge-white">Q<?=($model->atnq_product_quote_id)?></span> Attraction "<b>
            <?=\yii\helpers\Html::encode(!empty($model->atnq_product_details_json['product']['name']) ? $model->atnq_product_details_json['product']['name'] : ' - ')?></b>"
            <?php //=\yii\helpers\Html::encode($model->hqHotelList->hl_star)?>
            <?php //=\yii\helpers\Html::encode($model->atnqProductQuote->pq_name)?>
            <?php //=\yii\helpers\Html::encode($model->hq_destination_name ?? '')?>
             <?php //=\yii\helpers\Html::encode($model->atnqProductQuote->pq_gid)?>

        | <?= ProductQuoteStatus::asFormat($model->atnqProductQuote->pq_status_id) ?>

        <i class="ml-2 fas fa-donate" title="Profit Amount"></i> <?= $model->atnqProductQuote->pq_profit_amount ?>

        <?php if ($model->atnqProductQuote->pq_clone_id) : ?>
            <span class="badge badge-warning" style="padding-left: 5px">CLONE</span>
        <?php endif;?>

        <ul class="nav navbar-right panel_toolbox">
<!--            <li>-->
<!--                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>-->
<!--            </li>-->
            <li class="dropdown dropdown-offer-menu" data-product-quote-id="<?=($model->atnq_product_quote_id)?>" data-lead-id="<?=($attractionProduct->atnProduct->pr_lead_id)?>" data-url="<?= Url::to(['/offer/offer/list-menu-ajax'])?>">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="far fa-handshake"></i> Offers</a>
                <div class="dropdown-menu" role="menu">
                    <?php // ajax loaded content ?>
                </div>
            </li>

            <li class="dropdown dropdown-order-menu" data-product-quote-id="<?=($model->atnq_product_quote_id)?>" data-lead-id="<?=($attractionProduct->atnProduct->pr_lead_id)?>" data-url="<?= Url::to(['/order/order/list-menu-ajax'])?>">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fas fa-money-check-alt"></i> Orders</a>
                <div class="dropdown-menu" role="menu">
                    <?php // ajax loaded content ?>
                </div>
            </li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-bars text-warning"></i></a>
                <div class="dropdown-menu" role="menu">
                    <h6 class="dropdown-header">Quote Q<?=($model->atnq_product_quote_id)?></h6>

                    <?= Html::a('<i class="fa fa-search"></i> Details', null, [
                        'class' => 'btn-attraction-quote-details dropdown-item',
                        'data-id' => $model->atnq_product_quote_id,
                        'data-title' => '<i class="fas fa-archway"></i> ' . $model->atnqAttraction->atn_destination,
                        'data-url' => Url::to(['/attraction/attraction-quote/ajax-quote-details', 'id' => $model->atnq_product_quote_id]),
                        'title' => 'Details'
                    ]) ?>

                    <!--<?/*= Html::a('<i class="glyphicon glyphicon-remove-circle text-warning"></i> Clone quote', null, [
                        'class' => 'dropdown-item text-warning btn-clone-product-quote',
                        'data-product-quote-id' => $model->atnq_product_quote_id,
                        'data-attraction-quote-id' => $model->atnq_id,
                        'data-product-id' => $model->atnqProductQuote->pq_product_id,
                    ]) */?> -->

                    <?php if ($model->isBookable()) : ?>
                        <?= Html::a(
                            '<i class="fa fa-share-square"></i> Book',
                            null,
                            [
                                'class' => 'dropdown-item btn-attraction-book-quote',
                                'data-url' => Url::to('/attraction/attraction-quote/ajax-book'),
                                'data-attraction-quote-id' => $model->atnq_id,
                                'data-product-id' => $model->atnqProductQuote->pq_product_id,
                            ]
                        ) ?>
                    <?php endif; ?>
                    <?php if (!$model->atnqProductQuote->isNew()) : ?>
                        <?= Html::a(
                            '<i class="fa fa-share-square"></i> Check Booking Status',
                            null,
                            [
                            'class' => 'dropdown-item js-btn-attraction-booking-state',
                            'data-url' => Url::to('/attraction/attraction-quote/ajax-booking-state'),
                            'data-quote-id' => $model->atnq_id,
                            ]
                        ) ?>
                    <?php endif; ?>

                    <?php if ($model->isBooking()) : ?>
                        <?= Html::a(
                            '<i class="fa fa-share-square"></i> Cancel Book',
                            null,
                            [
                                'class' => 'dropdown-item text-danger btn-attraction-cancel-book-quote',
                                'data-url' => Url::to('/attraction/attraction-quote/ajax-cancel-book'),
                                'data-attraction-quote-id' => $model->atnq_id,
                                'data-product-id' => $model->atnqProductQuote->pq_product_id,
                            ]
                        ) ?>
                    <?php endif; ?>

                    <?= Html::a('<i class="fa fa-plus-circle"></i> Add option', null, [
                        'class' => 'dropdown-item text-success btn-add-product-quote-option',
                        'data-url' => Url::to(['/product/product-quote-option/create-ajax', 'id' => $model->atnq_product_quote_id]),
                    ]) ?>

                    <?= Html::a(
                        '<i class="fa fa-list"></i> API Service Log',
                        null,
                        [
                            'class' => 'dropdown-item text-secondary btn-product-api-service-log',
                            'data-url' => Url::to(['/attraction/hotel-quote-service-log/hotel-quote-log', 'id' => $model->atnq_id]),
                            'data-attraction-quote-id' => $model->atnq_id,
                            'data-product-id' => $model->atnqProductQuote->pq_product_id,
                        ]
                    )?>

                    <?= Html::a('<i class="fa fa-list"></i> Status log', null, [
                        'class' => 'dropdown-item text-secondary btn-product-quote-status-log',
                        'data-url' => Url::to(['/product/product-quote-status-log/show', 'gid' => $model->atnqProductQuote->pq_gid]),
                        'data-gid' => $model->atnqProductQuote->pq_gid,
                    ]) ?>

                    <?php if (Auth::can('/attraction/attraction-quote/ajax-file-generate') && $model->isBooking()) : ?>
                        <?= Html::a(
                            '<i class="fa fa-file-pdf-o"></i> Generate PDF',
                            null,
                            [
                                'class' => 'dropdown-item js-btn-generate-pdf-attraction-quote',
                                'data-url' => Url::to('/attraction/attraction-quote/ajax-file-generate'),
                                'data-quote-id' => $model->atnq_id,
                            ]
                        ) ?>
                    <?php endif; ?>

                    <div class="dropdown-divider"></div>
                    <?= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Delete quote', null, [
                        'class' => 'dropdown-item text-danger btn-delete-product-quote',
                        'data-product-quote-id' => $model->atnq_product_quote_id,
                        'data-attraction-quote-id' => $model->atnq_id,
                        'data-product-id' => $model->atnqProductQuote->pq_product_id,
                    ]) ?>
                </div>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block">

        <?php /*= $this->render('../quotes/quote_list', [
            'dataProvider' => $quotesProvider,
            'lead' => $lead,
            'leadForm' => $leadForm,
            'is_manager' => $is_manager,
        ])*/ ?>
        <i class="fa fa-user"></i> <?=$model->atnqProductQuote->pqCreatedUser ? Html::encode($model->atnqProductQuote->pqCreatedUser->username) : '-'?>,
        <i class="fa fa-calendar fa-info-circle"></i> <?=Yii::$app->formatter->asDatetime(strtotime($model->atnqProductQuote->pq_created_dt)) ?>
        <!--<i title="code: <?php /*=\yii\helpers\Html::encode($model->atnq_hash_key)*/?>">Hash: <?php /* =\yii\helpers\Html::encode($model->atnq_hash_key)*/?></i>-->



        <?php if ($model->atnqProductQuote) : ?>
            <?php Pjax::begin(['id' => 'pjax-quote_prices-' . $model->atnq_id, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
            <?= $this->render('_quote_prices', [
                'attractionQuote' => $model,
                'quote' => $model->atnqProductQuote,
                'priceData' => AttractionQuoteHelper::getPricesData($model)
            ]); ?>
            <?php Pjax::end(); ?>
        <?php endif; ?>

        <?= $this->render('@frontend/views/lead/quotes/partial/_quote_option_list', ['productQuote' => $model->atnqProductQuote]) ?>
        <?= $this->render('@frontend/views/lead/quotes/partial/_quote_total', ['productQuote' => $model->atnqProductQuote]) ?>

    </div>
</div>
    <?php Pjax::end(); ?>

<?php endif; ?>


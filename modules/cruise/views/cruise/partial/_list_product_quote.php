<?php

/* @var $this yii\web\View */
/* @var $index int */
/* @var $key int */
/* @var $cruiseProduct Cruise */
/* @var $model CruiseQuote */

use kartik\editable\Editable;
use modules\cruise\src\entity\cruise\Cruise;
use modules\cruise\src\entity\cruiseQuote\CruiseQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionStatus;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\web\View;
use yii\helpers\Html;
use yii\widgets\Pjax;

?>

<?php if ($model->productQuote) : ?>
    <?php
    $js = <<<JS
    $('body').off('click', '.btn-book-quote').on('click', '.btn-book-quote', function (e) {

        if(!confirm('Are you sure you want to book this quote?')) {
            return '';
        }

        e.preventDefault();
        $('#preloader').removeClass('d-none');
        let quoteId = $(this).data('cruise-quote-id');
        let productId = $(this).data('product-id');
        
        $.ajax({
          url: $(this).data('url'),
          type: 'post',
          data: {'id': quoteId},
          cache: false,
          dataType: 'json',
        }).done(function(data) {
            if (parseInt(data.status) === 1) {
                new PNotify({
                    title: 'The quote was successfully booking',
                    type: 'success',
                    text: data.message,
                    hide: true
                });
                pjaxReload({
                    container: '#pjax-product-quote-list-' + productId
                });
            } else {
                new PNotify({
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
    
    $('body').off('click', '.btn-cancel-book-quote').on('click', '.btn-cancel-book-quote', function (e) {

        if(!confirm('Are you sure you want to cancel book this quote?')) {
            return '';
        }

        e.preventDefault();
        $('#preloader').removeClass('d-none');
        let quoteId = $(this).data('cruise-quote-id');
        let productId = $(this).data('product-id');
        
        $.ajax({
          url: $(this).data('url'),
          type: 'post',
          data: {'id': quoteId},
          cache: false,
          dataType: 'json',
        }).done(function(data) {
            if (parseInt(data.status) === 1) {
                new PNotify({
                    title: 'Booking is canceled',
                    type: 'success',
                    text: data.message,
                    hide: true
                });
                pjaxReload({
                    container: '#pjax-product-quote-list-' + productId
                });
            } else {
                new PNotify({
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
        let quoteId = $(this).data('cruise-quote-id');
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
JS;

    $this->registerJs($js, \yii\web\View::POS_READY);
    ?>

    <?php Pjax::begin(['id' => 'pjax-product-quote-' . $model->productQuote->pq_id, 'timeout' => 2000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
<div class="x_panel">
    <div class="x_title">

        <span class="badge badge-white">Q<?=($model->crq_product_quote_id)?></span>
        Cruise "<b><?=\yii\helpers\Html::encode($model->crq_data_json['cruiseLine']['name'])?></b>"
        | <b><?= $model->crq_data_json['ship']['name'] ?></b>
        | "<b><?=\yii\helpers\Html::encode($cruiseProduct->crs_destination_label)?></b>"
        | <?= date('F j, Y', strtotime($model->crq_data_json['departureDate'])) ?> - <?= date('F j, Y', strtotime($model->crq_data_json['returnDate']))?>
        | <?= ProductQuoteStatus::asFormat($model->productQuote->pq_status_id) ?>

        <i class="ml-2 fas fa-donate" title="Profit Amount"></i> <?= $model->productQuote->pq_profit_amount ?>

        <ul class="nav navbar-right panel_toolbox">
            <li class="dropdown dropdown-offer-menu" data-product-quote-id="<?=($model->crq_product_quote_id)?>" data-lead-id="<?=($cruiseProduct->product->pr_lead_id)?>" data-url="<?= Url::to(['/offer/offer/list-menu-ajax'])?>">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="far fa-handshake"></i> Offers</a>
                <div class="dropdown-menu" role="menu">
                    <?php // ajax loaded content ?>
                </div>
            </li>

            <li class="dropdown dropdown-order-menu" data-product-quote-id="<?=($model->crq_product_quote_id)?>" data-lead-id="<?=($cruiseProduct->product->pr_lead_id)?>" data-url="<?= Url::to(['/order/order/list-menu-ajax'])?>">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fas fa-money-check-alt"></i> Orders</a>
                <div class="dropdown-menu" role="menu">
                    <?php // ajax loaded content ?>
                </div>
            </li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-bars text-warning"></i></a>
                <div class="dropdown-menu" role="menu">
                    <h6 class="dropdown-header">Quote Q<?=($model->crq_product_quote_id)?></h6>

                    <?php /*= Html::a('<i class="glyphicon glyphicon-remove-circle text-warning"></i> Clone quote', null, [
                        'class' => 'dropdown-item text-warning btn-clone-product-quote',
                        'data-product-quote-id' => $model->crq_product_quote_id,
                        'data-cruise-quote-id' => $model->crq_id,
                        'data-product-id' => $model->productQuote->pq_product_id,
                    ]) */ ?>

                    <?php /*= Html::a('<i class="fa fa-plus-circle"></i> Add option', null, [
                        'class' => 'dropdown-item text-success btn-add-product-quote-option',
                        //'data-product-quote-id' => $model->hq_product_quote_id,
                        'data-url' => Url::to(['/product/product-quote-option/create-ajax', 'id' => $model->crq_product_quote_id]),
                        //'data-product-id' => $model->hqProductQuote->pq_product_id,
                    ]) */ ?>

                    <?php /* if ($model->isBookable()) : ?>
                        <?= Html::a(
                            '<i class="fa fa-share-square"></i> Book',
                            null,
                            [
                                'class' => 'dropdown-item btn-book-quote',
                                'data-url' => Url::to('/cruise/cruise-quote/ajax-book'),
                                'data-cruise-quote-id' => $model->crq_id,
                                'data-cruise-id' => $model->productQuote->pq_product_id,
                            ]
                        ) ?>
                    <?php endif; */ ?>
                    <?php /* if ($model->isBooking()) : ?>
                        <?= Html::a(
                            '<i class="fa fa-share-square"></i> Cancel Book',
                            null,
                            [
                                'class' => 'dropdown-item text-danger btn-cancel-book-quote',
                                'data-url' => Url::to('/cruise/cruise-quote/ajax-cancel-book'),
                                'data-cruise-quote-id' => $model->crq_id,
                                'data-product-id' => $model->productQuote->pq_product_id,
                            ]
                        ) ?>
                    <?php endif;*/  ?>

                    <?php /*= Html::a(
                        '<i class="fa fa-list"></i> API Service Log',
                        null,
                        [
                            'class' => 'dropdown-item text-secondary btn-product-api-service-log',
                            'data-url' => Url::to(['/cruise/cruise-quote-service-log/cruise-quote-log', 'id' => $model->crq_id]),
                            'data-cruise-quote-id' => $model->crq_id,
                            'data-product-id' => $model->productQuote->pq_product_id,
                        ]
                    ) */ ?>

                    <?php  /*= Html::a('<i class="fa fa-list"></i> Status log', null, [
                        'class' => 'dropdown-item text-secondary btn-product-quote-status-log',
                        'data-url' => Url::to(['/product/product-quote-status-log/show', 'gid' => $model->productQuote->pq_gid]),
                        'data-gid' => $model->productQuote->pq_gid,
                    ]) */ ?>

                    <div class="dropdown-divider"></div>
                    <?= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Delete quote', null, [
                        'class' => 'dropdown-item text-danger btn-delete-product-quote',
                        'data-product-quote-id' => $model->crq_product_quote_id,
                        'data-cruise-quote-id' => $model->crq_id,
                        'data-product-id' => $model->productQuote->pq_product_id,
                    ]) ?>
                </div>
            </li>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block">

        <i class="fa fa-user"></i> <?=$model->productQuote->pqCreatedUser ? Html::encode($model->productQuote->pqCreatedUser->username) : '-'?>,
        <i class="fa fa-calendar fa-info-circle"></i> <?=Yii::$app->formatter->asDatetime(strtotime($model->productQuote->pq_created_dt)) ?>,
        <i title="code: <?=\yii\helpers\Html::encode($model->crq_hash_key)?>">Hash: <?=\yii\helpers\Html::encode($model->crq_hash_key)?></i>

            <div class="overflow_auto" style="overflow: auto">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th width="250">Cabin Name</th>
                        <th>Price, USD</th>
                        <th>Adult</th>
                        <th>Children</th>
                    </tr>
                    <tr>
                        <th><?= $model->crq_data_json['cabin']['name'] ?></th>
                        <th><?= number_format($model->crq_data_json['cabin']['price'], 2) ?></th>
                        <th><?= $model->getAdults() ?></th>
                        <th><?= $model->getChildren() ?></th>
                    </tr>
                    <tr>
                        <td class="text-right">Total: </td>
                        <td class="text-left" colspan="3"><?= number_format(($model->crq_data_json['cabin']['price'] * ($model->getAdults() + $model->getChildren())), 2) ?></td>
                    </tr>

                </table>
            </div>

    </div>
</div>
    <?php Pjax::end(); ?>

<?php endif; ?>
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
use sales\auth\Auth;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\web\View;
use yii\helpers\Html;
use yii\widgets\Pjax;

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
                 pjaxReload({container: "#pjax-lead-offers"});
                 pjaxReload({container: "#pjax-lead-orders"});
                 /*pjaxReload({container: "#pjax-lead-products-wrap"});*/
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
                new PNotify({
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
                new PNotify({
                    title: 'Document have been successfully generated',
                    type: 'success',
                    text: data.message,
                    hide: true
                });                
                addFileToFileStorageList();                
            } else {
                new PNotify({
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
JS;

    $this->registerJs($js, \yii\web\View::POS_READY);
    ?>

    <?php Pjax::begin(['id' => 'pjax-product-quote-' . $model->atnqProductQuote->pq_id, 'timeout' => 2000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
<div class="x_panel">
    <div class="x_title">
        <span class="badge badge-white">Q<?=($model->atnq_product_quote_id)?></span> Attraction "<b><?=\yii\helpers\Html::encode($model->atnq_attraction_name)?></b>"
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



        <?php if ($model->atnqProductQuote) :
            $totalAmountRoom = 0;
            $adlTotalCount = 0;
            $chdTotalCount = 0;
            $totalNp = 0;
            $totalMkp = 0;
            $totalExMkp = 0;
            ?>
            <div class="overflow_auto" style="overflow: auto">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>ID</th>
                        <th>Type Name</th>
                        <th>Supplier Name</th>
                        <th>NP, $</th>
                        <!-- <th>Adult</th>
                        <th>Children</th>
                        <th>Cancel Amount</th>

                        <th>Mkp, $</th>
                        <th>Ex Mkp, $</th>
                        <th>SFP, %</th>
                        <th>SFS, $</th>
                        <th>SP, $</th>-->
                    </tr>
                    <?php /*foreach ($model->hotelQuoteRooms as $room) :
                        $totalAmountRoom += (float) $room->hqr_amount;
                        $adlTotalCount += $room->hqr_adults;
                        $chdTotalCount += $room->hqr_children;
                        $totalNp += $room->hqr_amount;
                        $totalMkp += $room->hqr_system_mark_up;
                        $totalExMkp += $room->hqr_agent_mark_up;

                        $sfs = round(($room->hqr_amount + $room->hqr_system_mark_up + $room->hqr_agent_mark_up) * $room->hqr_service_fee_percent / 100, 2);
                        */?>

                        <tr>
                            <td title="<?=Html::encode($model->atnq_id)?>"><?=Html::encode($model->atnq_id)?></td>
                            <td ><?=Html::encode($model->atnq_type_name) ?></td>
                            <td ><?=Html::encode($model->atnq_supplier_name) ?></td>
                            <td ><?=number_format($model->atnqProductQuote->pq_origin_price, 2) ?></td>

                            <!--<td title="code: <? /*=Html::encode('$room->hqr_board_code')*/?>">
                                <? /*=Html::encode('$room->hqr_board_name')*/?>
                                <?php /*if ('$room->hqr_rate_comments') :*/?>
                                    <i class="fa fa-info-circle green" title="Rate Comments: <? /*=Html::encode('$room->hqr_rate_comments')*/?>"></i>
                                <?php /*endif;*/?>
                            </td> -->


                            <!-- <td>
                                 <? /*= Editable::widget([
                                    'name' => 'extra_markup[' . $room->hqr_id . ']',
                                    'asPopover' => false,
                                    'pjaxContainerId' => 'pjax-product-quote-' . $model->hqProductQuote->pq_id,
                                    'value' => number_format($room->hqr_agent_mark_up, 2),
                                    'header' => 'Extra markup',
                                    'size' => 'sm',
                                    'inputType' => Editable::INPUT_TEXT,
                                    'buttonsTemplate' => '{submit}',
                                    'pluginEvents' => ['editableSuccess' => "function(event, val, form, data) { pjaxReload({container: '#pjax-product-quote-{$model->hqProductQuote->pq_id}'}); }",],
                                    'inlineSettings' => [
                                        'templateBefore' => '<div class="editable-pannel">{loading}',
                                        'templateAfter' => '{buttons}{close}</div>'],
                                    'options' => ['class' => 'form-control','style' => 'width:50px;', 'placeholder' => 'Enter extra markup','resetButton' => '<i class="fa fa-ban"></i>'],
                                    'formOptions' => [
                                        'action' => Url::toRoute(['/hotel/hotel-quote/ajax-update-agent-markup'])
                                    ]
                                ]) */?>
                            </td>-->
                           <!-- <td><?/*= Html::encode('$room->hqr_service_fee_percent') */?>%</td>-->
                            <!--<td><?/*= '$sfs' */?></td>-->
                            <!--<td class="text-right"><?/*='number_format($room->hqr_amount + $room->hqr_system_mark_up + $room->hqr_agent_mark_up + $sfs, 2)'*/?> <?/*=Html::encode('$room->hqr_currency')*/?></td>-->
                        </tr>
                    <?php /*endforeach; */?>
                    <tr>
                        <td colspan="3" class="text-right">Position Total: </td>
                        <td ><?= number_format($model->atnqProductQuote->pq_origin_price, 2) ?></td>
                        <!--<td class="text-center"><?/*='$adlTotalCount' ? '<i class="fa fa-user"></i> ' . '$adlTotalCount' : '-'*/?></td>
                        <td class="text-center"><?/*='$chdTotalCount' ? '<i class="fa fa-child"></i> ' . '$chdTotalCount' : '-'*/?></td>
                        <td class="text-right"></td>
                        <td class="text-right"><?/*= 'number_format($totalNp, 2)' */?></td>
                        <td class="text-right"><?/*= 'number_format($totalMkp, 2)' */?></td>
                        <td class="text-right"><?/*= 'number_format($totalExMkp, 2)' */?></td>
                        <td class="text-right"></td>
                        <td class="text-right"><?/*= 'number_format($model->hqProductQuote->pq_service_fee_sum, 2)' */?></td>-->

                        <?php
/*                        $price = round((float) $model->hqProductQuote->pq_price, 2);
                        $totalAmountRoom = round($totalAmountRoom, 2);
                        */?>

                        <!-- <td class="text-right <?='( $totalAmountRoom !== $price)' ? 'danger' : ''?>">
                           <b title="<?/*=$totalAmountRoom*/?> & <?/*=$price*/?>"><?/*=number_format($price, 2)*/?> USD</b>
                        </td>-->
                    </tr>
                </table>
            </div>
        <?php endif; ?>



        <?= $this->render('@frontend/views/lead/quotes/partial/_quote_option_list', ['productQuote' => $model->atnqProductQuote]) ?>
        <?= $this->render('@frontend/views/lead/quotes/partial/_quote_total', ['productQuote' => $model->atnqProductQuote]) ?>

    </div>
</div>
    <?php Pjax::end(); ?>

<?php endif; ?>


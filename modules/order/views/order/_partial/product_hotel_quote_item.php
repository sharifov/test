<?php

/* @var $this yii\web\View */
/* @var $index int */
/* @var $key int */
/* @var $hotelProduct Hotel */
/* @var $model ProductQuote */

use kartik\editable\Editable;
use modules\hotel\models\Hotel;
use modules\hotel\models\HotelQuote;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionStatus;
use src\auth\Auth;
use src\helpers\product\ProductQuoteHelper;
use src\services\CurrencyHelper;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use yii\web\View;
use yii\helpers\Html;
use yii\widgets\Pjax;

?>


    <?php
    $js = <<<JS
    $('body').off('click', '.btn-book-quote').on('click', '.btn-book-quote', function (e) {

        if(!confirm('Are you sure you want to book this quote?')) {
            return '';
        }

        e.preventDefault();
        $('#preloader').removeClass('d-none');
        let quoteId = $(this).data('hotel-quote-id');
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
    
    $('body').off('click', '.js-btn-generate-pdf-quote').on('click', '.js-btn-generate-pdf-quote', function (e) {

        if(!confirm('Are you sure you want to generate documents?')) {
            return '';
        }

        e.preventDefault();
        $('#preloader').removeClass('d-none');
        let quoteId = $(this).data('hotel-quote-id');
                
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
    
    $('body').off('click', '.btn-cancel-book-quote').on('click', '.btn-cancel-book-quote', function (e) {

        if(!confirm('Are you sure you want to cancel book this quote?')) {
            return '';
        }

        e.preventDefault();
        $('#preloader').removeClass('d-none');
        let quoteId = $(this).data('hotel-quote-id');
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
    
    $(document).on('click', '.btn-product-api-service-log', function(e){        
        e.preventDefault();
        let url = $(this).data('url');
        let quoteId = $(this).data('hotel-quote-id');
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
    
    $('body').on('click','.btn-hotel-quote-details', function (e) {
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

            <?= ProductQuoteHelper::displayOriginOrAlternativeIcon($model) ?>

            <span class="badge badge-white">Q<?=($model->pq_id)?></span> Hotel "<b><?=\yii\helpers\Html::encode($model->hotelQuote->hqHotelList->hl_name)?></b>"
            (<?=\yii\helpers\Html::encode($model->hotelQuote->hqHotelList->hl_star)?>),
            <?=\yii\helpers\Html::encode($model->hq_destination_name ?? '')?>
            | <?= ProductQuoteStatus::asFormat($model->pq_status_id) ?>

            <i class="ml-2 fas fa-donate" title="Profit Amount"></i> <?= $model->pq_profit_amount ?>

            <?php if ($model->pq_clone_id) : ?>
                <span class="badge badge-warning" style="padding-left: 5px">CLONE</span>
            <?php endif;?>

            <ul class="nav navbar-right panel_toolbox">
                <!--            <li>-->
                <!--                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>-->
                <!--            </li>-->
                <!--<li class="dropdown dropdown-offer-menu" data-product-quote-id="<?/*=($model->pq_id)*/?>" data-lead-id="<?/*=($model->pqProduct->pr_lead_id)*/?>" data-url="<?/*= Url::to(['/offer/offer/list-menu-ajax'])*/?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="far fa-handshake"></i> Offers</a>
                    <div class="dropdown-menu" role="menu">
                        <?php /*// ajax loaded content*/?>
                    </div>
                </li>

                <li class="dropdown dropdown-order-menu" data-product-quote-id="<?/*=($model->pq_id)*/?>" data-lead-id="<?/*=($model->pqProduct->pr_lead_id)*/?>" data-url="<?/*= Url::to(['/order/order/list-menu-ajax'])*/?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fas fa-money-check-alt"></i> Orders</a>
                    <div class="dropdown-menu" role="menu">
                        <?php /*// ajax loaded content*/?>
                    </div>
                </li>-->

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-bars text-warning"></i></a>
                    <div class="dropdown-menu" role="menu">
                        <h6 class="dropdown-header">Quote Q<?=($model->pq_id)?></h6>
                        <?= Html::a('<i class="fa fa-search"></i> Details', null, [
                            'class' => 'btn-hotel-quote-details dropdown-item',
                            'data-id' => $model->pq_id,
                            'data-title' => '<i class="fa fa-hotel"></i> ' . $model->hotelQuote->hq_hotel_name,
                            'data-url' => Url::to(['/hotel/hotel-quote/ajax-quote-details', 'id' => $model->pq_id]),
                            'title' => 'Details'
                        ]) ?>

                        <!--<?/*= Html::a('<i class="glyphicon glyphicon-remove-circle text-warning"></i> Clone quote', null, [
                            'class' => 'dropdown-item text-warning btn-clone-product-quote',
                            'data-product-quote-id' => $model->pq_id,
                            'data-hotel-quote-id' => $model->hotelQuote->hq_id,
                            'data-product-id' => $model->pqProduct->pr_id,
                        ]) */?>

                        <?/*= Html::a('<i class="fa fa-plus-circle"></i> Add option', null, [
                            'class' => 'dropdown-item text-success btn-add-product-quote-option',
                            //'data-product-quote-id' => $model->hq_product_quote_id,
                            'data-url' => Url::to(['/product/product-quote-option/create-ajax', 'id' => $model->hotelQuote->hq_product_quote_id]),
                            //'data-product-id' => $model->hqProductQuote->pq_product_id,
                        ]) */?>

                        <?php /*if ($model->isBookable()) : */?>
                            <?/*= Html::a(
                                '<i class="fa fa-share-square"></i> Book',
                                null,
                                [
                                    'class' => 'dropdown-item btn-book-quote',
                                    'data-url' => Url::to('/hotel/hotel-quote/ajax-book'),
                                    'data-hotel-quote-id' => $model->hotelQuote->hq_id,
                                    'data-product-id' => $model->pqProduct->pr_id,
                                ]
                            ) */?>
                        <?php /*endif; */?>-->
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
            <i class="fa fa-user"></i> <?=$model->pqCreatedUser ? Html::encode($model->pqCreatedUser->username) : '-'?>,
            <i class="fa fa-calendar fa-info-circle"></i> <?=Yii::$app->formatter->asDatetime(strtotime($model->pq_created_dt)) ?>,
            <i title="code: <?=\yii\helpers\Html::encode($model->hotelQuote->hq_hash_key)?>">Hash: <?=\yii\helpers\Html::encode($model->hotelQuote->hq_hash_key)?></i>

            <?php if ($model->hotelQuote->hotelQuoteRooms) :
                $totalAmountRoom = 0;
                $adlTotalCount = 0;
                $chdTotalCount = 0;
                $totalNp = 0;
                $totalMkp = 0;
                $totalExMkp = 0;
                $totalSfs = 0;
                $totalSp = 0;
                ?>
                <div class="overflow_auto" style="overflow: auto">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th>ID</th>
                            <th>Room Name</th>
                            <th>Board Name</th>
                            <th>Pax</th>
                            <th>Cancel Amount</th>
                            <th>NP, $</th>
                            <th>Mkp, $</th>
                            <th>Ex Mkp, $</th>
                            <th>SFP, %</th>
                            <th>SFS, $</th>
                            <th>SP, $</th>
                        </tr>
                        <?php foreach ($model->hotelQuote->hotelQuoteRooms as $room) :
                            $totalAmountRoom += (float) $room->hqr_amount;
                            $adlTotalCount += $room->hqr_adults;
                            $chdTotalCount += $room->hqr_children;
                            $totalNp += $room->hqr_amount;
                            $totalMkp += $room->hqr_system_mark_up;
                            $totalExMkp += $room->hqr_agent_mark_up;

                            $sfs = round(($room->hqr_amount + $room->hqr_system_mark_up + $room->hqr_agent_mark_up) * $room->hqr_service_fee_percent / 100, 2);
                            $totalSfs += $sfs;

                            $sp = CurrencyHelper::roundUp($room->hqr_amount + $room->hqr_system_mark_up + $room->hqr_agent_mark_up + $sfs);
                            $totalSp += $sp;
                            ?>

                            <tr>

                                <td title="<?=Html::encode($room->hqr_key)?>"><?=Html::encode($room->hqr_id)?></td>
                                <td title="code: <?=Html::encode($room->hqr_code)?>, class: <?=Html::encode($room->hqr_class)?>"><?=Html::encode($room->hqr_room_name)?></td>

                                <td title="code: <?=Html::encode($room->hqr_board_code)?>">
                                    <?=Html::encode($room->hqr_board_name)?>
                                    <?php if ($room->hqr_rate_comments) :?>
                                        <i class="fa fa-info-circle green" title="Rate Comments: <?=Html::encode($room->hqr_rate_comments)?>"></i>
                                    <?php endif;?>
                                </td>
                                <td class="text-center">
                                    <?=$room->hqr_adults ? '<i class="fa fa-user"></i> ' . ($room->hqr_adults) : '-'?>
                                    <?=$room->hqr_children ? ', <i class="fa fa-child"></i> ' . ($room->hqr_children) : '-'?>
                                </td>
                                <td>
                                    <?php if ($room->getActualCancelAmount()) : ?>
                                        <?=Html::encode($room->getActualCancelAmount())?>, <?=Html::encode($room->getActualCancelDate())?>
                                    <?php endif; ?>
                                </td>
                                <td><?= Html::encode($room->hqr_amount) ?></td>
                                <td><?= Html::encode($room->hqr_system_mark_up) ?></td>
                                <td>
                                    <!-- <?/*= Editable::widget([
                                        'name' => 'extra_markup[' . $room->hqr_id . ']',
                                        'asPopover' => false,
                                        'pjaxContainerId' => 'pjax-product-quote-' . $model->pq_id,
                                        'value' => number_format($room->hqr_agent_mark_up, 2),
                                        'header' => 'Extra markup',
                                        'size' => 'sm',
                                        'inputType' => Editable::INPUT_TEXT,
                                        'buttonsTemplate' => '{submit}',
                                        'pluginEvents' => ['editableSuccess' => "function(event, val, form, data) { pjaxReload({container: '#pjax-product-quote-{$model->pq_id}'}); }",],
                                        'inlineSettings' => [
                                            'templateBefore' => '<div class="editable-pannel">{loading}',
                                            'templateAfter' => '{buttons}{close}</div>'],
                                        'options' => ['class' => 'form-control','style' => 'width:50px;', 'placeholder' => 'Enter extra markup','resetButton' => '<i class="fa fa-ban"></i>'],
                                        'formOptions' => [
                                            'action' => Url::toRoute(['/hotel/hotel-quote/ajax-update-agent-markup'])
                                        ]
                                    ]) */?>-->
                                    <?= number_format($room->hqr_agent_mark_up, 2) ?>
                                </td>
                                <td><?= Html::encode($room->hqr_service_fee_percent) ?>%</td>
                                <td><?= $sfs ?></td>
                                <!--                    <td>--><?php ////=Html::encode($room->hqr_id)?><!--</td>-->
                                <td class="text-right"><?= $sp ?> <?=Html::encode($room->hqr_currency)?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="3" class="text-right">Room Total: </td>
                            <td class="text-center">
                                <?=$adlTotalCount ? '<i class="fa fa-user"></i> ' . $adlTotalCount : '-'?>
                                <?=$chdTotalCount ? ', <i class="fa fa-child"></i> ' . $chdTotalCount : '-'?>
                            </td>
                            <td class="text-right"></td>
                            <td class="text-right"><?= number_format($totalNp, 2) ?></td>
                            <td class="text-right"><?= number_format($totalMkp, 2) ?></td>
                            <td class="text-right"><?= number_format($totalExMkp, 2) ?></td>
                            <td class="text-right"></td>
                            <td class="text-right"><?= number_format($totalSfs, 2) ?></td>

                            <?php
                            $price = round((float) $model->pq_price, 2);
                            $totalAmountRoom = round($totalAmountRoom, 2);
                            ?>

                            <td class="text-right">
                                <b><?=number_format($totalSp, 2)?> USD</b>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right">Total: </td>
                            <td class="text-center">
                                <?=$adlTotalCount ? '<i class="fa fa-user"></i> ' . $adlTotalCount : '-'?>
                                <?=$chdTotalCount ? ', <i class="fa fa-child"></i> ' . $chdTotalCount : '-'?>
                            </td>
                            <td class="text-right"></td>
                            <td class="text-right"><?= number_format($totalNp * $model->hotelQuote->getCountDays(), 2) ?></td>
                            <td class="text-right"><?= number_format($totalMkp * $model->hotelQuote->getCountDays(), 2) ?></td>
                            <td class="text-right"><?= number_format($totalExMkp * $model->hotelQuote->getCountDays(), 2) ?></td>
                            <td class="text-right"></td>
                            <?php
                            $totalFeeSum = round($totalSfs * $model->hotelQuote->getCountDays(), 2);
                            $feeSum = round((float) $model->pq_service_fee_sum, 2);
                            ?>

                            <td class="text-right <?=($totalFeeSum !== $feeSum) ? 'danger' : ''?>">
                                <b title="<?=$totalFeeSum?> & <?=$feeSum?>"><?=number_format($feeSum, 2)?> USD</b>
                            </td>

                            <?php
                            $totalPrice = round($totalSp * $model->hotelQuote->getCountDays(), 2);
                            $price = round((float) $model->pq_price, 2);
                            ?>

                            <td class="text-right <?=($totalPrice !== $price) ? 'danger' : ''?>">
                                <b title="<?=$totalPrice?> & <?=$price?>"><?=number_format($price, 2)?> USD</b>
                            </td>
                        </tr>

                    </table>
                </div>
            <?php endif; ?>

            <?= $this->render('@frontend/views/lead/quotes/partial/_quote_option_list', ['productQuote' => $model]) ?>
            <?= $this->render('@frontend/views/lead/quotes/partial/_quote_total', ['productQuote' => $model]) ?>

        </div>
    </div>
    <?php Pjax::end(); ?>


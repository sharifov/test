<?php
/* @var $this yii\web\View */
/* @var $index int */
/* @var $key int */
/* @var $hotelProduct Hotel */
/* @var $model HotelQuote */

use modules\hotel\models\Hotel;
use modules\hotel\models\HotelQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionStatus;
use yii\data\ArrayDataProvider;
use yii\web\View;
use yii\helpers\Html;

?>

<?php if ($model->hqProductQuote): ?>

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
                new PNotify({
                    title: 'The quote was successfully booking',
                    type: 'success',
                    text: data.message,
                    hide: true
                });
                $.pjax.reload({
                    container: '#pjax-product-quote-list-' + productId
                });
            } else {
                new PNotify({
                    title: 'Booking failed',
                    type: 'error',
                    text: data.message,
                    hide: true
                });
                $.pjax.reload({
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
                new PNotify({
                    title: 'Booking is canceled',
                    type: 'success',
                    text: data.message,
                    hide: true
                });
                $.pjax.reload({
                    container: '#pjax-product-quote-list-' + productId
                });
            } else {
                new PNotify({
                    title: 'Process failed',
                    type: 'error',
                    text: data.message,
                    hide: true
                });
                $.pjax.reload({
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
JS;

$this->registerJs($js, \yii\web\View::POS_READY);
?>

<div class="x_panel">
    <div class="x_title">

        <span class="badge badge-white">Q<?=($model->hq_product_quote_id)?></span> Hotel "<b><?=\yii\helpers\Html::encode($model->hqHotelList->hl_name)?></b>"
            (<?=\yii\helpers\Html::encode($model->hqHotelList->hl_star)?>),
            <?php //=\yii\helpers\Html::encode($model->hqProductQuote->pq_name)?>
            <?=\yii\helpers\Html::encode($model->hq_destination_name ?? '')?>
             <?php //=\yii\helpers\Html::encode($model->hqProductQuote->pq_gid)?>

        | <?= ProductQuoteStatus::asFormat($model->hqProductQuote->pq_status_id) ?>

        <?php if ($model->hqProductQuote->pq_clone_id): ?>
            <span class="badge badge-warning" style="padding-left: 5px">CLONE</span>
        <?php endif;?>

        <ul class="nav navbar-right panel_toolbox">
<!--            <li>-->
<!--                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>-->
<!--            </li>-->
            <li class="dropdown dropdown-offer-menu" data-product-quote-id="<?=($model->hq_product_quote_id)?>" data-lead-id="<?=($hotelProduct->phProduct->pr_lead_id)?>" data-url="<?=\yii\helpers\Url::to(['/offer/offer/list-menu-ajax'])?>">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="far fa-handshake"></i> Offers</a>
                <div class="dropdown-menu" role="menu">
                    <?php // ajax loaded content ?>
                </div>
            </li>

            <li class="dropdown dropdown-order-menu" data-product-quote-id="<?=($model->hq_product_quote_id)?>" data-lead-id="<?=($hotelProduct->phProduct->pr_lead_id)?>" data-url="<?=\yii\helpers\Url::to(['/order/order/list-menu-ajax'])?>">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fas fa-money-check-alt"></i> Orders</a>
                <div class="dropdown-menu" role="menu">
                    <?php // ajax loaded content ?>
                </div>
            </li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-cog"></i></a>
                <div class="dropdown-menu" role="menu">
                    <h6 class="dropdown-header">Quote Q<?=($model->hq_product_quote_id)?></h6>
                    <?php /*= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Update Request', null, [
                                'class' => 'dropdown-item text-danger btn-update-product',
                                'data-product-id' => $product->pr_id
                            ])*/ ?>
                   <?php /* <div class="dropdown-divider"></div>

                    <!-- Level three dropdown-->
                    <div class="dropdown-submenu">
                        <a id="dropdownMenu<?=($model->hq_product_quote_id)?>" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">Add to Offer</a>
                        <div aria-labelledby="dropdownMenu<?=($model->hq_product_quote_id)?>" class="dropdown-menu">
                            <a href="#" class="dropdown-item">3rd level</a>
                            <a href="#" class="dropdown-item">3rd level</a>
                        </div>
                    </div>*/ ?>
                    <!-- End Level three -->

<!--                    <ul>-->
<!--                        <li class="dropdown">-->
<!--                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Dropdown</a>-->
<!--                            <div class="dropdown-menu" role="menu">-->
<!--                                <a href="#" class="dropdown-item"><i class="fa fa-cog"></i> aaa</a>-->
<!--                            </div>-->
<!--                        </li>-->
<!--                    </ul>-->

                    <?= Html::a('<i class="glyphicon glyphicon-remove-circle text-warning"></i> Clone quote', null, [
                        'class' => 'dropdown-item text-warning btn-clone-product-quote',
                        'data-product-quote-id' => $model->hq_product_quote_id,
                        'data-hotel-quote-id' => $model->hq_id,
                        'data-product-id' => $model->hqProductQuote->pq_product_id,
                    ]) ?>

                    <?= Html::a('<i class="fa fa-plus-circle"></i> Add option', null, [
                        'class' => 'dropdown-item text-success btn-add-product-quote-option',
                        //'data-product-quote-id' => $model->hq_product_quote_id,
                        'data-url' => \yii\helpers\Url::to(['/product/product-quote-option/create-ajax', 'id' => $model->hq_product_quote_id]),
                        //'data-product-id' => $model->hqProductQuote->pq_product_id,
                    ]) ?>

                    <?php if ($model->isBookable()): ?>
                        <?= Html::a(
                            '<i class="fa fa-share-square"></i> Book',
                             null,
                            [
                                'class' => 'dropdown-item btn-book-quote',
                                'data-url' => \yii\helpers\Url::to('/hotel/hotel-quote/ajax-book'),
                                'data-hotel-quote-id' => $model->hq_id,
                                'data-product-id' => $model->hqProductQuote->pq_product_id,
                            ]
                        ) ?>
                    <? endif; ?>
                    <?php if ($model->isBooking()): ?>
                        <?= Html::a('<i class="fa fa-share-square"></i> Cancel Book',null,
                            [
                                'class' => 'dropdown-item text-danger btn-cancel-book-quote',
                                'data-url' => \yii\helpers\Url::to('/hotel/hotel-quote/ajax-cancel-book'),
                                'data-hotel-quote-id' => $model->hq_id,
                                'data-product-id' => $model->hqProductQuote->pq_product_id,
                            ]
                        ) ?>
                    <? endif; ?>

                    <?= Html::a('<i class="fa fa-list-alt"></i> API Service Log', null,
                        [
                            'class' => 'dropdown-item text-secondary btn-product-api-service-log',
                            'data-url' => \yii\helpers\Url::to(['/hotel/hotel-quote-service-log/hotel-quote-log', 'id' => $model->hq_id]),
                            'data-hotel-quote-id' => $model->hq_id,
                            'data-product-id' => $model->hqProductQuote->pq_product_id,
                        ]
                    )?>

                    <?= Html::a('<i class="fa fa-list-alt"></i> Status log', null, [
                        'class' => 'dropdown-item text-secondary btn-product-quote-status-log',
                        'data-url' => \yii\helpers\Url::to(['/product/product-quote-status-log/show', 'gid' => $model->hqProductQuote->pq_gid]),
                        'data-gid' => $model->hqProductQuote->pq_gid,
                    ]) ?>

                    <div class="dropdown-divider"></div>
                    <?= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Delete quote', null, [
                        'class' => 'dropdown-item text-danger btn-delete-product-quote',
                        'data-product-quote-id' => $model->hq_product_quote_id,
                        'data-hotel-quote-id' => $model->hq_id,
                        'data-product-id' => $model->hqProductQuote->pq_product_id,
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
        <i class="fa fa-user"></i> <?=$model->hqProductQuote->pqCreatedUser ? Html::encode($model->hqProductQuote->pqCreatedUser->username) : '-'?>,
        <i class="fa fa-calendar fa-info-circle"></i> <?=Yii::$app->formatter->asDatetime(strtotime($model->hqProductQuote->pq_created_dt)) ?>,
        <i title="code: <?=\yii\helpers\Html::encode($model->hq_hash_key)?>">Hash: <?=\yii\helpers\Html::encode($model->hq_hash_key)?></i>

        <?php if ($model->hotelQuoteRooms):
            $totalAmountRoom = 0;
            $adlTotalCount = 0;
            $chdTotalCount = 0;
            ?>
            <table class="table table-striped table-bordered">
                <?php foreach ($model->hotelQuoteRooms as $room):

                    $totalAmountRoom += (float) $room->hqr_amount;
                    $adlTotalCount += $room->hqr_adults;
                    $chdTotalCount += $room->hqr_children;
                    ?>

<!--                    'hqr_id',-->
<!--                    'hqr_hotel_quote_id',-->
<!--                    'hqr_room_name',-->
<!--                    'hqr_key',-->
<!--                    'hqr_code',-->
<!--                    'hqr_class',-->
<!--                    'hqr_amount',-->
<!--                    'hqr_currency',-->
<!--                    'hqr_cancel_amount',-->
<!--                    'hqr_cancel_from_dt',-->
<!--                    'hqr_payment_type',-->
<!--                    'hqr_board_code',-->
<!--                    'hqr_board_name',-->
<!--                    'hqr_rooms',-->
<!--                    'hqr_adults',-->
<!--                    'hqr_children',-->

                <tr>
<!--                    <td>-->
<!--                        --><?php ////=Html::encode($room->hqr_id)?>
<!--                        --><?php
//                            //\yii\helpers\VarDumper::dump($room->attributes, 10, true);
//                        ?>
<!--                    </td>-->

                    <td title="<?=Html::encode($room->hqr_key)?>"><?=Html::encode($room->hqr_id)?></td>
                    <td title="code: <?=Html::encode($room->hqr_code)?>, class: <?=Html::encode($room->hqr_class)?>"><?=Html::encode($room->hqr_room_name)?></td>


<!--                    <td>--><?php //=Html::encode($room->hqr_payment_type)?><!--</td>-->
                    <td title="code: <?=Html::encode($room->hqr_board_code)?>">
                        <?=Html::encode($room->hqr_board_name)?>
                        <?php if ($room->hqr_rate_comments):?>
                            <i class="fa fa-info-circle green" title="Rate Comments: <?=Html::encode($room->hqr_rate_comments)?>"></i>
                        <?php endif;?>
                    </td>
                    <td class="text-center"><?=$room->hqr_adults ? '<i class="fa fa-user"></i> ' . ($room->hqr_adults) : '-'?></td>
                    <td class="text-center"><?=$room->hqr_children ? '<i class="fa fa-child"></i> ' . ($room->hqr_children) : '-'?></td>
                    <td>
                        <?php if ($room->hqr_cancel_amount): ?>
                        <?=Html::encode($room->hqr_cancel_amount)?>, <?=Html::encode($room->hqr_cancel_from_dt)?>
                        <?php endif; ?>
                    </td>
<!--                    <td>--><?php ////=Html::encode($room->hqr_id)?><!--</td>-->
                    <td class="text-right"><?=number_format($room->hqr_amount, 2)?> <?=Html::encode($room->hqr_currency)?></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" class="text-right">Room Total: </td>
                    <td class="text-center"><?=$adlTotalCount ? '<i class="fa fa-user"></i> '. $adlTotalCount : '-'?></td>
                    <td class="text-center"><?=$chdTotalCount ? '<i class="fa fa-child"></i> '. $chdTotalCount : '-'?></td>
                    <td class="text-right"></td>

                    <?php
                        $originPrice = round((float) $model->hqProductQuote->pq_origin_price, 2);
                        $totalAmountRoom = round($totalAmountRoom, 2);
                    ?>

                    <td class="text-right <?=( $totalAmountRoom !== $originPrice) ? 'danger': ''?>">
                        <b title="<?=$totalAmountRoom?> & <?=$originPrice?>"><?=number_format($originPrice, 2)?> USD</b>
                    </td>
                </tr>
            </table>
        <?php endif; ?>


        <?php
            $totalAmountOption = 0;
            $totalClientAmountOption = 0;
            $totalExtraMarkupOption = 0;
        ?>


        <?php if ($model->hqProductQuote->productQuoteOptions): ?>
            <h2>Options</h2>
            <table class="table table-striped table-bordered">
                <tr>
                    <th>ID</th>
                    <th>Option</th>
                    <th>Name / Description</th>
                    <th>Status</th>
                    <th style="width: 120px">Extra markup</th>
                    <th style="width: 120px">Price</th>
                    <th style="width: 52px"></th>

                </tr>
                <?php foreach ($model->hqProductQuote->productQuoteOptions as $quoteOption):
                    $totalAmountOption += (float) $quoteOption->pqo_price;
                    $totalClientAmountOption += (float) $quoteOption->pqo_client_price;
                    $totalExtraMarkupOption += (float) $quoteOption->pqo_extra_markup;
                    ?>
                <tr>
                    <td style="width: 60px" title="<?=Html::encode($quoteOption->pqo_id)?>"><?=Html::encode($quoteOption->pqo_id)?></td>
                    <td style="width: 120px"><?=$quoteOption->pqoProductOption ? Html::encode($quoteOption->pqoProductOption->po_name) : '' ?></td>
                    <td>
                        <b><?=Html::encode($quoteOption->pqo_name)?></b>
                        <?=$quoteOption->pqo_description ? '<br>'. Html::encode($quoteOption->pqo_description) . '' : ''?>
                    </td>
                    <td class="text-center" style="width: 120px"><?= ProductQuoteOptionStatus::asFormat($quoteOption->pqo_status_id)?></td>
                    <td class="text-right" title="Extra Markup"><?=number_format($quoteOption->pqo_extra_markup, 2)?> USD</td>
                    <td class="text-right"><?=number_format($quoteOption->pqo_price, 2)?> USD</td>
<!--                    <td class="text-right">--><?php //=number_format($quoteOption->pqo_client_price, 2)?><!-- --><?php //=Html::encode($model->hqProductQuote->pq_client_currency)?><!--</td>-->
                    <td>
                        <?php
                        echo Html::a('<i class="fa fa-edit text-warning" title="Update"></i>', null, [
                            'class' => 'btn-update-product-quote-option',
                            'data-url' => \yii\helpers\Url::to(['/product/product-quote-option/update-ajax', 'id' => $quoteOption->pqo_id])
                        ]);
                        ?>

                        <?php
                        echo Html::a('<i class="glyphicon glyphicon-remove-circle text-danger" title="Remove"></i>', null, [
                            'data-pqo-id' => $quoteOption->pqo_id,
                            'data-product-id' => $model->hqProductQuote->pq_product_id,
                            'class' => 'btn-delete-product-quote-option',
                            'data-url' => \yii\helpers\Url::to(['/product/product-quote-option/delete-ajax'])
                        ]);
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <th colspan="4" class="text-right">Option Total: </th>
                    <?php
                        $totalAmountOption = round($totalAmountOption, 2);
                        $totalClientAmountOption = round($totalClientAmountOption, 2);
                        $totalExtraMarkupOption = round($totalExtraMarkupOption, 2);
                    ?>

                    <th class="text-right" title="Extra Markup">
                        <?=number_format($totalExtraMarkupOption, 2)?> USD
                    </th>
                    <th class="text-right">
                        <?=number_format($totalAmountOption, 2)?> USD
                    </th>
<!--                    <td class="text-right">-->
<!--                        <b>--><?php //=number_format($totalClientAmount, 2)?><!-- --><?php //=Html::encode($model->hqProductQuote->pq_client_currency)?><!--</b>-->
<!--                    </td>-->
                    <th></th>
                </tr>
            </table>
        <?php endif; ?>
        <hr>
        <?php
            $totalAmount = round($totalAmountRoom + $totalAmountOption + $totalExtraMarkupOption, 2);
        ?>
        <div class="text-right"><h4>Total: <?=number_format($totalAmount, 2)?> USD</h4></div>



    </div>
</div>

<?php endif; ?>
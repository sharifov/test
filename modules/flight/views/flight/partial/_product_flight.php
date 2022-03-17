<?php

use modules\product\src\entities\product\Product;
use common\widgets\Alert;
use modules\flight\models\Flight;
use modules\flight\models\forms\ItineraryEditForm;
use modules\flight\src\helpers\FlightFormatHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $product Product */
/* @var  $pjaxRequest bool */



/*\yii\web\YiiAsset::register($this);


$searchModel = new HotelQuoteSearch();
$params = Yii::$app->request->queryParams;
$params['HotelQuoteSearch']['hq_hotel_id'] = $model->ph_id;
$dataProviderQuotes = $searchModel->searchProduct($params);*/

$pjaxId = 'pjax-product-' . $product->pr_id;
$pjaxRequest = $pjaxRequest ?? false;

$chevronClass = $pjaxRequest ? 'fa fa-chevron-up' : 'fa fa-chevron-down'
?>
<?php \yii\widgets\Pjax::begin(['id' => $pjaxId,  'enablePushState' => false, 'enableReplaceState' => false, 'timeout' => 4000])?>
    <div class="x_panel">
        <div class="x_title">
            <h2>
                <a class="collapse-link">
                    <i class="<?= Html::encode($product->getIconClass()) ?>" title="ID: <?=$product->pr_id?>"></i> <?=Html::encode($product->prType->pt_name)?> <?=$product->pr_name ? ' - ' . Html::encode($product->pr_name) : ''?>
                    <?php if ($product->flight->flightQuotes) : ?>
                        <sup title="Number of quotes">(<?=count($product->flight->flightQuotes)?>)</sup>
                    <?php endif;?>
                </a>
                <?php if ($product->pr_description) :?>
                    <a  id="product_description_<?=$product->pr_id?>"
                        class="popover-class fa fa-info-circle text-info"
                        data-toggle="popover" data-html="true" data-trigger="hover" data-placement="top"
                        data-container="body" title="<?=Html::encode($product->pr_name)?>"
                        data-content='<?=Html::encode($product->pr_description)?>'
                    ></a>
                <?php endif; ?>
            </h2>
            <ul class="nav navbar-right panel_toolbox">
                <?php //php if ($is_manager) : ?>
                    <li>
                        <span style="font-size: 13px; padding: 5px; display: flex; align-items: center;color: #596b7d;">
                             <?php if ($segments = $product->flight->flightSegments) :?>
                                    <?php if (isset($segments[0]) && $segments[0]->fs_origin_iata) : ?>
                                     (<b><?= Html::encode($segments[0]->fs_origin_iata) ?></b>)
                                    <?php endif; ?>
                             <?php endif; ?>
                        </span>
                    </li>
                    <li>
                        <span style="font-size: 13px; padding: 5px; display: flex; align-items: center;color: #596b7d;">
                            <?php
                            switch ($product->flight->fl_trip_type_id) {
                                case Flight::TRIP_TYPE_ONE_WAY:
                                    $iconClass = 'fa fa-long-arrow-right';
                                    break;
                                case Flight::TRIP_TYPE_ROUND_TRIP:
                                    $iconClass = 'fa fa-exchange';
                                    break;
                                case Flight::TRIP_TYPE_MULTI_DESTINATION:
                                    $iconClass = 'fa fa-random';
                                    break;
                                default:
                                    $iconClass = '';
                            }
                            ?>
                            <i class="<?=$iconClass?> text-success" aria-hidden="true" style="margin-right: 10px;"></i>
                            <?php if ($product->flight->fl_trip_type_id) :?>
                                <?= FlightFormatHelper::tripTypeName($product->flight->fl_trip_type_id) ?> •&nbsp;
                            <?php endif; ?>

                            <?php if ($product->flight->fl_cabin_class) :?>
                                <b><?= FlightFormatHelper::cabinName($product->flight->fl_cabin_class) ?></b>&nbsp;•&nbsp;
                            <?php endif; ?>

                            <?= (int)$product->flight->fl_adults + (int)$product->flight->fl_children + (int)$product->flight->fl_infants ?> pax
                        </span>
                    </li>
                    <li>
                        <span style="font-size: 13px; padding: 5px; display: flex; align-items: center;color: #596b7d;">
                            <?php if ($product->flight->fl_adults) : ?>
                                <span style="font-size: 12px; color: #596b7d;display: flex;align-items: center;"><strong class="label label-success"
                                                                                       style="margin-left: 7px;padding: 4px 6px;margin-right: 2px;"
                                              style="margin-left: 7px;"><?= $product->flight->fl_adults ?></strong> ADT</span>
                            <?php endif; ?>
                                <?php if ($product->flight->fl_children) : ?>
                                    <span style="font-size: 12px; color: #596b7d;display: flex;align-items: center;"><strong class="label label-success"
                                                                                           style="margin-left: 7px;padding: 4px 6px;margin-right: 2px;"
                                                  style="margin-left: 7px;"><?= $product->flight->fl_children ?></strong> CHD</span>
                                <?php endif; ?>
                                <?php if ($product->flight->fl_infants) : ?>
                                    <span style="font-size: 12px; color: #596b7d;display: flex;align-items: center;"><strong class="label label-success"
                                                                                           style="margin-left: 7px;padding: 4px 6px;margin-right: 2px;"
                                                  style="margin-left: 7px;"><?= $product->flight->fl_infants ?></strong> INF</span>
                                <?php endif; ?>
                        </span>
                    </li>
                    <li>
                            <span style="font-size: 13px; padding: 5px; display: flex; align-items: center;color: #596b7d;">
                                 <?php if ($segments = $product->flight->flightSegments) :?>
                                        <?php if (isset($segments[0]) && $segments[0]->fs_departure_date) : ?>
                                         <b><?= Yii::$app->formatter->asDate(strtotime($segments[0]->fs_departure_date)) ?></b>
                                        <?php endif; ?>
                                 <?php endif; ?>
                            </span>
                    </li>
                    <li>
                        <div style="margin-right: 50px"></div>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-bars warning"></i> <span class="text-warning">Actions</span></a>
                        <div class="dropdown-menu" role="menu">
                            <?php /*= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Update Request', null, [
                                'class' => 'dropdown-item text-danger btn-update-product',
                                'data-product-id' => $product->pr_id
                            ])*/ ?>

                            <h6 class="dropdown-header">P<?=$product->pr_id?> - F<?=$product->flight->fl_id?></h6>

                            <?= Html::a('<i class="fa fa-edit"></i> Update Request', null, [
                                'data-url' => Url::to([
                                    '/flight/flight/ajax-update-itinerary-view',
                                    'id' => $product->flight->fl_id
                                ]),
                                                         'data-flight-id' => $product->flight->fl_id,
                                                         'data-pjax-id' => $pjaxId,
                                                         'class' => 'dropdown-item text-warning btn-update-flight-request btn-update-request',
                                                         'data-product-id' => $product->pr_id
                            ]) ?>

                            <?= Html::a('<i class="fa fa-search"></i> Search Quotes', null, [
                                'data-url' => \yii\helpers\Url::to([
                                    '/flight/flight-quote/ajax-search-quote',
                                    'id' => $product->flight->fl_id
                                ]),
                                'data-pjax-id' => $pjaxId,
                                'class' => 'dropdown-item text-success btn-search-flight-quotes'
                            ]) ?>

                            <?= Html::a('<i class="fa fa-plus"></i> Add Quote', null, [
                                'data-url' => \yii\helpers\Url::to([
                                    '/flight/flight-quote/ajax-add-quote-content'
                                ]),
                                'data-flight-id' => $product->flight->fl_id,
                                'data-lead-id' => $product->pr_lead_id,
                                'data-pjax-reload-id' => $pjaxId,
                                'class' => 'dropdown-item text-success btn-add-flight-quote'
                            ]) ?>

                            <div class="dropdown-divider"></div>
                            <?= Html::a('<i class="fa fa-edit"></i> Update Product', null, [
                                'class' => 'dropdown-item text-warning btn-update-product',
                                'data-product-id' => $product->pr_id,
                            ]) ?>
                            <?php if ($product->isDeletable()) : ?>
                                <?= Html::a(
                                    '<i class="glyphicon glyphicon-remove-circle text-danger"></i> Delete Flight',
                                    null,
                                    [
                                        'class' => 'dropdown-item text-danger btn-delete-product',
                                        'data-product-id' => $product->pr_id
                                    ]
                                ) ?>
                            <?php endif ?>
                        </div>
                    </li>
                <?php //php endif; ?>
                <li>
                    <a class="collapse-link"><i class="<?= $chevronClass ?>"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" <?php if (!$pjaxRequest) :
            ?>style="display: none"<?php
                               endif; ?>>
            <div class="row">
                <div class="col-md-12">
                    <?= Alert::widget() ?>
                </div>
            </div>
            <?= $this->render('_view_flight_request', [ 'itineraryForm' => (new ItineraryEditForm($product->flight)) ]) ?>

            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-6">
                        <?= DetailView::widget([
                            'model' => $product->flight,
                            'attributes' => [
                                'fl_stops',
                                'fl_delayed_charge:booleanByLabel',
                            ],
                        ]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= DetailView::widget([
                            'model' => $product,
                            'attributes' => [
                                'pr_market_price',
                                'pr_client_budget',
                            ],
                        ]) ?>
                    </div>
                </div>
            </div>

            <?= $this->render('../../flight-quote/partial/_quote_list', ['product' => $product]) ?>

        </div>
    </div>
<?php \yii\widgets\Pjax::end()?>



<?php

$js = <<<JS

   $('body').off('click', '.btn-update-flight-request').on('click', '.btn-update-flight-request', function (e) {
       e.preventDefault();
       let updateHotelRequestUrl = $(this).data('url');
       let pjaxId = $(this).data('pjaxId');
       
       let modal = $('#modal-md');
       modal.find('.modal-body').html('');
       modal.find('.modal-title').html('Update flight request');
       modal.find('.modal-body').load(updateHotelRequestUrl, {pjaxIdWrap: pjaxId}, function( response, status, xhr ) {
           modal.modal({
             backdrop: 'static',
             show: true
           });
       });
   });
//
//    $('body').off('click', '.btn-add-flight-room').on('click', '.btn-add-flight-room', function (e) {
//        e.preventDefault();
//        let url = $(this).data('url');
//        //$('#preloader').removeClass('d-none');
//        
//        let modal = $('#modal-df');
//        modal.find('.modal-body').html('');
//        modal.find('.modal-title').html('Add Room request');
//        modal.find('.modal-body').load(url, function( response, status, xhr ) {
//            //$('#preloader').addClass('d-none');
//            modal.modal({
//              backdrop: 'static',
//              show: true
//            });
//        });
//    });
//    
//    $('body').off('click', '.btn-update-flight-room').on('click', '.btn-update-flight-room', function (e) {
//        e.preventDefault();
//        let url = $(this).data('url');
//                
//        let modal = $('#modal-df');
//        modal.find('.modal-body').html('');
//        modal.find('.modal-title').html('Update Room request');
//        modal.find('.modal-body').load(url, function( response, status, xhr ) {
//            //$('#preloader').addClass('d-none');
//            modal.modal({
//              backdrop: 'static',
//              show: true
//            });
//        });
//    });
//    
//    
     $('body').off('click', '.btn-search-flight-quotes').on('click', '.btn-search-flight-quotes', function (e) {
        e.preventDefault();
        $('#preloader').removeClass('d-none');          
        let url = $(this).data('url');
        let pjaxId = $(this).data('pjax-id');;
        let modal = $('#modal-lg');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Search flight Quotes');
        modal.find('.modal-body').load(url, {pjaxId: pjaxId}, function( response, status, xhr ) {
            if (status == 'error') {
                alert(response);
            } else {
                $('#preloader').addClass('d-none');
                modal.modal({
                  backdrop: 'static',
                  show: true
                });
            }
        });
    });
    
     $('body').off('click', '.btn-add-flight-quote').on('click', '.btn-add-flight-quote', function (e) {
        e.preventDefault();
        $('#preloader').removeClass('d-none');          
        let url = $(this).data('url');
        let flightId = $(this).data('flight-id');
        let leadId = $(this).data('lead-id');
        let pjaxReloadId = $(this).data('pjax-reload-id');
        let modal = $('#modal-md');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Add Quote');
        modal.find('.modal-body').load(url, {flightId: flightId, leadId: leadId, pjaxReloadId: pjaxReloadId}, function( response, status, xhr ) {
            $('#preloader').addClass('d-none');
            if (status == 'error') {
                createNotifyByObject({
                    'title': 'Error',
                    'type': 'error',
                    'text': xhr.statusText
                })
            } else {
                modal.modal({
                  backdrop: 'static',
                  show: true
                });
            }
        });
    });
//    
//    
//    $('body').off('click', '.btn-delete-flight-room').on('click', '.btn-delete-flight-room', function(e) {
//        
//        if(!confirm('Are you sure you want to delete this room?')) {
//            return '';
//        }
//        
//      e.preventDefault();
//      $('#preloader').removeClass('d-none');
//      let roomId = $(this).data('room-id');
//      let hotelId = $(this).data('hotel-id');
//      let url = $(this).data('url');
//     
//      /*alert(productId);
//      
//      let btnSubmit = $(this).find(':submit');
//      btnSubmit.prop('disabled', true);
//      btnSubmit.find('i').removeClass('fa-save').addClass('fa-spin fa-spinner');*/
//
//     // $('#preloader').removeClass('d-none');
//
//      $.ajax({
//          url: url,
//          type: 'post',
//          data: {'id': roomId},
//          dataType: 'json',
//      })
//          .done(function(data) {
//              if (data.error) {
//                  alert(data.error);
//                  createNotifyByObject({
//                        title: 'Error: delete room',
//                        type: 'error',
//                        text: data.error,
//                        hide: true
//                    });
//              } else {
//                  $.pjax.reload({
//                      container: '#pjax-flight-rooms-' + hotelId
//                  });
//                  createNotifyByObject({
//                        title: 'The room was successfully removed',
//                        type: 'success',
//                        text: data.message,
//                        hide: true
//                    });
//              }
//          })
//        .fail(function( jqXHR, textStatus ) {
//            alert( "Request failed: " + textStatus );
//        }).always(function() {
//            $('#preloader').addClass('d-none');
//        });
//      // return false;
//    });
    
    
JS;

$this->registerJs($js, \yii\web\View::POS_READY, 'update-flight-request-js');

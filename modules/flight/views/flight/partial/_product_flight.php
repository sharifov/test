<?php

use common\models\Product;
use modules\flight\models\Flight;
use modules\flight\models\forms\ItineraryEditForm;
use modules\flight\src\helpers\FlightFormatHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $product Product */



/*\yii\web\YiiAsset::register($this);


$searchModel = new HotelQuoteSearch();
$params = Yii::$app->request->queryParams;
$params['HotelQuoteSearch']['hq_hotel_id'] = $model->ph_id;
$dataProviderQuotes = $searchModel->searchProduct($params);*/

?>
<?//php \yii\widgets\Pjax::begin(['id' => 'pjax-product-' . $product->pr_id, 'enablePushState' => false, 'timeout' => 10000])?>
    <div class="x_panel">
        <div class="x_title">
            <h2>
                <i class="fas fa-plane" title="ID: <?=$product->pr_id?>"></i> <?=Html::encode($product->prType->pt_name)?> <?=$product->pr_name ? ' - ' . Html::encode($product->pr_name) : ''?>
                <?php if ($product->pr_description):?>
                    <i class="fa fa-info-circle text-info" title="<?=Html::encode($product->pr_description)?>"></i>
                <?php endif;?>
                (<?=count($product->flight->flightQuotes)?>)
            </h2>
            <ul class="nav navbar-right panel_toolbox">
                <?//php if ($is_manager) : ?>
                    <li>
                        <span style="font-size: 13px; padding: 5px; display: flex; align-items: center;color: #596b7d;">
                            <?php
                            switch ($product->flight->fl_trip_type_id) {
                                case Flight::TRIP_TYPE_ONE_WAY : $iconClass = 'fa fa-long-arrow-right';
                                    break;
                                case Flight::TRIP_TYPE_ROUND_TRIP : $iconClass = 'fa fa-exchange';
                                    break;
                                case Flight::TRIP_TYPE_MULTI_DESTINATION : $iconClass = 'fa fa-random';
                                    break;
                                default: $iconClass = '';
                            }
                            ?>
                            <i class="<?=$iconClass?> text-success" aria-hidden="true" style="margin-right: 10px;"></i>
                            <?= FlightFormatHelper::tripTypeName($product->flight->fl_trip_type_id) ?> •
                            <b><?= FlightFormatHelper::cabinName($product->flight->fl_cabin_class) ?></b> •
                            <?= (int)$product->flight->fl_adults + (int)$product->flight->fl_children + (int)$product->flight->fl_infants ?> pax
                        </span>
                    </li>
                    <li>
                        <span style="font-size: 13px; padding: 5px; display: flex; align-items: center;color: #596b7d;">
                            <?php if ($product->flight->fl_adults): ?>
                                <span style="font-size: 12px; color: #596b7d;display: flex;align-items: center;"><strong class="label label-success"
                                                                                       style="margin-left: 7px;padding: 4px 6px;margin-right: 2px;"
                                              style="margin-left: 7px;"><?= $product->flight->fl_adults ?></strong> ADT</span>
                            <?php endif; ?>
                                <?php if ($product->flight->fl_children): ?>
                                    <span style="font-size: 12px; color: #596b7d;display: flex;align-items: center;"><strong class="label label-success"
                                                                                           style="margin-left: 7px;padding: 4px 6px;margin-right: 2px;"
                                                  style="margin-left: 7px;"><?= $product->flight->fl_children ?></strong> CHD</span>
                                <?php endif; ?>
                                <?php if ($product->flight->fl_infants): ?>
                                    <span style="font-size: 12px; color: #596b7d;display: flex;align-items: center;"><strong class="label label-success"
                                                                                           style="margin-left: 7px;padding: 4px 6px;margin-right: 2px;"
                                                  style="margin-left: 7px;"><?= $product->flight->fl_infants ?></strong> INF</span>
                                <?php endif; ?>
                        </span>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-cog"></i></a>
                        <div class="dropdown-menu" role="menu">
                            <?/*= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Update Request', null, [
                                'class' => 'dropdown-item text-danger btn-update-product',
                                'data-product-id' => $product->pr_id
                            ])*/ ?>

<!--                            --><?//= Html::a('<i class="fa fa-search"></i> Search Quotes', null, [
//                                'data-url' => \yii\helpers\Url::to([
//                                    '/flight/flight-quote/search-ajax',
//                                    'id' => $product->flight->fl_id
//                                ]),
//                                'data-hotel-id' => $product->flight->fl_id,
//                                'class' => 'dropdown-item text-success btn-search-flight-quotes'
//                            ]) ?>

                            <?= Html::a('<i class="fa fa-edit"></i> Update Request', null, [
                                'data-url' => Url::to([
                                    '/flight/flight/ajax-update-itinerary-view',
                                    'id' => $product->flight->fl_id
                                ]),
                                'data-hotel-id' => $product->flight->fl_id,
                                'class' => 'dropdown-item text-warning btn-update-flight-request'
                            ]) ?>

                            <?= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Delete Flight',
                                null, [
                                    'class' => 'dropdown-item text-danger btn-delete-product',
                                    'data-product-id' => $product->pr_id
                                ]) ?>

                        </div>
                    </li>
                <?//php endif; ?>
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: none">
            <?= $this->render('_view_flight_request', [ 'itineraryForm' => (new ItineraryEditForm($product->flight)) ]) ?>
        </div>
    </div>
<?//php \yii\widgets\Pjax::end()?>



<?php

$js = <<<JS

   $('body').off('click', '.btn-update-flight-request').on('click', '.btn-update-flight-request', function (e) {
       e.preventDefault();
       let updateHotelRequestUrl = $(this).data('url');
       //$('#preloader').removeClass('d-none');
       
       let modal = $('#modal-md');
       modal.find('.modal-body').html('');
       modal.find('.modal-title').html('Update flight request');
       modal.find('.modal-body').load(updateHotelRequestUrl, function( response, status, xhr ) {
           //$('#preloader').addClass('d-none');
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
//     $('body').off('click', '.btn-search-flight-quotes').on('click', '.btn-search-flight-quotes', function (e) {
//        e.preventDefault();
//        $('#preloader').removeClass('d-none');          
//        let url = $(this).data('url');
//        let modal = $('#modal-lg');
//        modal.find('.modal-body').html('');
//        modal.find('.modal-title').html('Search flight Quotes');
//        modal.find('.modal-body').load(url, function( response, status, xhr ) {
//            $('#preloader').addClass('d-none');
//            modal.modal({
//              backdrop: 'static',
//              show: true
//            });
//        });
//    });
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
//                  new PNotify({
//                        title: 'Error: delete room',
//                        type: 'error',
//                        text: data.error,
//                        hide: true
//                    });
//              } else {
//                  $.pjax.reload({
//                      container: '#pjax-flight-rooms-' + hotelId
//                  });
//                  new PNotify({
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

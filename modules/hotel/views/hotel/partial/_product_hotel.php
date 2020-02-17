<?php

use modules\product\src\entities\product\Product;
use modules\hotel\models\search\HotelQuoteSearch;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $product Product */



/*\yii\web\YiiAsset::register($this);


$searchModel = new HotelQuoteSearch();
$params = Yii::$app->request->queryParams;
$params['HotelQuoteSearch']['hq_hotel_id'] = $model->ph_id;
$dataProviderQuotes = $searchModel->searchProduct($params);*/

?>
<?php //php \yii\widgets\Pjax::begin(['id' => 'pjax-product-' . $product->pr_id, 'enablePushState' => false, 'timeout' => 10000])?>
    <div class="x_panel">
        <div class="x_title">
            <h2>
                <i class="fas fa-hotel" title="ID: <?=$product->pr_id?>"></i> <?=Html::encode($product->prType->pt_name)?> <?=$product->pr_name ? ' - ' . Html::encode($product->pr_name) : ''?>
                <?php if ($product->pr_description):?>
                    <i class="fa fa-info-circle text-info" title="<?=Html::encode($product->pr_description)?>"></i>
                <?php endif;?>
                (<?=count($product->productQuotes)?>)
            </h2>
            <ul class="nav navbar-right panel_toolbox">
                <?php //php if ($is_manager) : ?>
                    <!--                    <li>-->
                    <!--                        --><?php //=Html::a('<i class="fa fa-plus-circle success"></i> Add Quote', null, ['class' => 'add-clone-alt-quote', 'data-uid' => 0, 'data-url' => Url::to(['quote/create', 'leadId' => $leadForm->getLead()->id, 'qId' => 0])])?>
                    <!--                    </li>-->
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-cog"></i></a>
                        <div class="dropdown-menu" role="menu">
                            <?php /*= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Update Request', null, [
                                'class' => 'dropdown-item text-danger btn-update-product',
                                'data-product-id' => $product->pr_id
                            ])*/ ?>

                            <?= Html::a('<i class="fa fa-search"></i> Search Quotes', null, [
                                'data-url' => \yii\helpers\Url::to([
                                    '/hotel/hotel-quote/search-ajax',
                                    'id' => $product->hotel->ph_id
                                ]),
                                'data-hotel-id' => $product->hotel->ph_id,
                                'class' => 'dropdown-item text-success btn-search-hotel-quotes'
                            ]) ?>

                            <?= Html::a('<i class="fa fa-edit"></i> Update Request', null, [
                                'data-url' => \yii\helpers\Url::to([
                                    '/hotel/hotel/update-ajax',
                                    'id' => $product->hotel->ph_id
                                ]),
                                'data-hotel-id' => $product->hotel->ph_id,
                                'class' => 'dropdown-item text-warning btn-update-hotel-request'
                            ]) ?>


                            <?= Html::a('<i class="fa fa-plus"></i> Add Room', null, [
                                'data-url' => \yii\helpers\Url::to([
                                    '/hotel/hotel-room/create-ajax',
                                    'id' => $product->hotel->ph_id,
                                ]),
                                'data-hotel-id' => $product->hotel->ph_id,
                                'class' => 'dropdown-item btn-add-hotel-room'
                            ]) ?>


                            <?= Html::a('<i class="glyphicon glyphicon-remove-circle text-danger"></i> Delete product',
                                null, [
                                    'class' => 'dropdown-item text-danger btn-delete-product',
                                    'data-product-id' => $product->pr_id
                                ]) ?>

                        </div>
                    </li>
                <?php //php endif; ?>
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: none">
            <?php //php if ((int) $product->pr_type_id === \common\models\ProductType::PRODUCT_HOTEL && $product->hotel): ?>
                <?php \yii\widgets\Pjax::begin(['id' => 'pjax-product-search-' . $product->pr_id, 'enablePushState' => false, 'timeout' => 5000])?>
                <?= $this->render('_view_search', [
                    'model' => $product->hotel,
                    //'dataProviderQuotes' => $dataProviderQuotes
                    //'dataProviderRooms'
                ]) ?>
                <?php \yii\widgets\Pjax::end();?>
            <?php //php endif; ?>
        </div>
    </div>
<?php //php \yii\widgets\Pjax::end()?>



<?php

$js = <<<JS

    $('body').off('click', '.btn-update-hotel-request').on('click', '.btn-update-hotel-request', function (e) {
        e.preventDefault();
        let updateHotelRequestUrl = $(this).data('url');
        //$('#preloader').removeClass('d-none');
        
        let modal = $('#modal-sm');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Update Hotel request');
        modal.find('.modal-body').load(updateHotelRequestUrl, function( response, status, xhr ) {
            //$('#preloader').addClass('d-none');
            modal.modal({
              backdrop: 'static',
              show: true
            });
        });
    });

    $('body').off('click', '.btn-add-hotel-room').on('click', '.btn-add-hotel-room', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        //$('#preloader').removeClass('d-none');
        
        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Add Room request');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            //$('#preloader').addClass('d-none');
            if (status == 'error') {
                alert(response);
            } else {
                modal.modal({
                  backdrop: 'static',
                  show: true
                });
            }
        });
    });
    
    $('body').off('click', '.btn-update-hotel-room').on('click', '.btn-update-hotel-room', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
                
        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Update Room request');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
            //$('#preloader').addClass('d-none');
            if (status == 'error') {
                alert(response);
            } else {
                modal.modal({
                  backdrop: 'static',
                  show: true
                });
            }
        });
    });
    
    
     $('body').off('click', '.btn-search-hotel-quotes').on('click', '.btn-search-hotel-quotes', function (e) {
        e.preventDefault();
        $('#preloader').removeClass('d-none');          
        let url = $(this).data('url');
        let modal = $('#modal-lg');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Search Hotel Quotes');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
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
    
    
    $('body').off('click', '.btn-delete-hotel-room').on('click', '.btn-delete-hotel-room', function(e) {
        
        if(!confirm('Are you sure you want to delete this room?')) {
            return '';
        }
        
      e.preventDefault();
      $('#preloader').removeClass('d-none');
      let roomId = $(this).data('room-id');
      let hotelId = $(this).data('hotel-id');
      let url = $(this).data('url');
     
      /*alert(productId);
      
      let btnSubmit = $(this).find(':submit');
      btnSubmit.prop('disabled', true);
      btnSubmit.find('i').removeClass('fa-save').addClass('fa-spin fa-spinner');*/

     // $('#preloader').removeClass('d-none');

      $.ajax({
          url: url,
          type: 'post',
          data: {'id': roomId},
          dataType: 'json',
      })
          .done(function(data) {
              if (data.error) {
                  alert(data.error);
                  new PNotify({
                        title: 'Error: delete room',
                        type: 'error',
                        text: data.error,
                        hide: true
                    });
              } else {
                  $.pjax.reload({
                      container: '#pjax-hotel-rooms-' + hotelId
                  });
                  new PNotify({
                        title: 'The room was successfully removed',
                        type: 'success',
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
      // return false;
    });
    
    $(function() {
  
        $('body').off('show.bs.dropdown', '.dropdown-offer-menu').on('show.bs.dropdown', '.dropdown-offer-menu', function () {
            let menu = $(this);
            let productQuoteId = menu.data('product-quote-id');
            let leadId = menu.data('lead-id');
            let url = menu.data('url');
            menu.find('.dropdown-menu').html('<a href="#" class="dropdown-item"><i class="fa fa-spin fa-spinner"></i> Loading ...</a>');
            
            $.ajax({
                  url: url,
                  type: 'post',
                  data: {'product_quote_id': productQuoteId, 'lead_id': leadId},
                  dataType: 'json',
              })
                  .done(function(data) {
                      if (data.error) {
                          //alert(data.error);
                          new PNotify({
                                title: 'Error: offer menu',
                                type: 'error',
                                text: data.error,
                                hide: true
                            });
                      } else {
                          menu.find('.dropdown-menu').html(data.html);
                      }
                  })
                .fail(function( jqXHR, textStatus ) {
                    alert( "Request failed: " + textStatus );
                });
        });
        
        $('body').off('show.bs.dropdown', '.dropdown-order-menu').on('show.bs.dropdown', '.dropdown-order-menu', function () {
            let menu = $(this);
            let productQuoteId = menu.data('product-quote-id');
            let leadId = menu.data('lead-id');
            let url = menu.data('url');
            menu.find('.dropdown-menu').html('<a href="#" class="dropdown-item"><i class="fa fa-spin fa-spinner"></i> Loading ...</a>');
            
            $.ajax({
                  url: url,
                  type: 'post',
                  data: {'product_quote_id': productQuoteId, 'lead_id': leadId},
                  dataType: 'json',
              })
                  .done(function(data) {
                      if (data.error) {
                          //alert(data.error);
                          new PNotify({
                                title: 'Error: order menu',
                                type: 'error',
                                text: data.error,
                                hide: true
                            });
                      } else {
                          menu.find('.dropdown-menu').html(data.html);
                      }
                  })
                .fail(function( jqXHR, textStatus ) {
                    alert( "Request failed: " + textStatus );
                });
        });
        
        
        $('body').off('click', '.btn-add-quote-to-offer').on('click', '.btn-add-quote-to-offer', function (e) {
            e.preventDefault();
            let menu = $(this);
            let productQuoteId = menu.data('product-quote-id');
            let offerId = menu.data('offer-id');
            let url = menu.data('url');
            
            //alert(quoteId);
            //menu.find('.dropdown-menu').html('<a href="#" class="dropdown-item"><i class="fa fa-spin fa-spinner"></i> Loading ...</a>');
            $('#preloader').removeClass('d-none');
            
            $.ajax({
                  url: url,
                  type: 'post',
                  data: {'product_quote_id': productQuoteId, 'offer_id': offerId},
                  dataType: 'json',
              })
                  .done(function(data) {
                      if (data.error) {
                          //alert(data.error);
                          new PNotify({
                                title: 'Error: offer transfer',
                                type: 'error',
                                text: data.error,
                                hide: true
                            });
                      } else {
                          
                          $.pjax.reload({container: '#pjax-lead-offers', timout: 8000});
                          new PNotify({
                                title: 'Quote was successfully added',
                                type: 'success',
                                text: data.message,
                                hide: true
                            });
                      }
                  })
                .fail(function( jqXHR, textStatus ) {
                    alert( "Request failed: " + textStatus );
                }).always(function() {
                    //btnSubmit.prop('disabled', false);
                    //btnSubmit.find('i').removeClass('fa-spin fa-spinner').addClass('fa-save');
                    //alert( "complete" );
                    $('#preloader').addClass('d-none');
                });
              // return false;
            //});
            
            //alert(123);
        });
        
        
        $('body').off('click', '.btn-add-quote-to-order').on('click', '.btn-add-quote-to-order', function (e) {
            e.preventDefault();
            let menu = $(this);
            let productQuoteId = menu.data('product-quote-id');
            let orderId = menu.data('order-id');
            let url = menu.data('url');
            
            //alert(quoteId);
            //menu.find('.dropdown-menu').html('<a href="#" class="dropdown-item"><i class="fa fa-spin fa-spinner"></i> Loading ...</a>');
            $('#preloader').removeClass('d-none');
            
            $.ajax({
                  url: url,
                  type: 'post',
                  data: {'product_quote_id': productQuoteId, 'order_id': orderId},
                  dataType: 'json',
              })
                  .done(function(data) {
                      if (data.error) {
                          //alert(data.error);
                          new PNotify({
                                title: 'Error: order',
                                type: 'error',
                                text: data.error,
                                hide: true
                            });
                      } else {
                          
                          $.pjax.reload({container: '#pjax-lead-orders', timout: 8000});
                          new PNotify({
                                title: 'Quote was successfully added',
                                type: 'success',
                                text: data.message,
                                hide: true
                            });
                      }
                  })
                .fail(function( jqXHR, textStatus ) {
                    alert( "Request failed: " + textStatus );
                }).always(function() {
                    //btnSubmit.prop('disabled', false);
                    //btnSubmit.find('i').removeClass('fa-spin fa-spinner').addClass('fa-save');
                    //alert( "complete" );
                    $('#preloader').addClass('d-none');
                });
              // return false;
            //});
            
            //alert(123);
        });
        
        
        $('body').off('click', '.btn-delete-quote-from-offer').on('click', '.btn-delete-quote-from-offer', function (e) {
            
            if(!confirm('Are you sure you want to delete this quote from offer?')) {
                return '';
            }
            
            e.preventDefault();
            let menu = $(this);
            let productQuoteId = menu.data('product-quote-id');
            let offerId = menu.data('offer-id');
            let url = menu.data('url');
            
            //alert(quoteId);
            //menu.find('.dropdown-menu').html('<a href="#" class="dropdown-item"><i class="fa fa-spin fa-spinner"></i> Loading ...</a>');
            $('#preloader').removeClass('d-none');
            
            $.ajax({
                  url: url,
                  type: 'post',
                  data: {'product_quote_id': productQuoteId, 'offer_id': offerId},
                  dataType: 'json',
              })
                  .done(function(data) {
                      if (data.error) {
                          //alert(data.error);
                          new PNotify({
                                title: 'Error: delete quote from offer',
                                type: 'error',
                                text: data.error,
                                hide: true
                            });
                      } else {
                          
                          $.pjax.reload({container: '#pjax-lead-offers', timout: 8000});
                          new PNotify({
                                title: 'Quote was successfully deleted',
                                type: 'success',
                                text: data.message,
                                hide: true
                            });
                      }
                  })
                .fail(function( jqXHR, textStatus ) {
                    alert( "Request failed: " + textStatus );
                }).always(function() {
                    //btnSubmit.prop('disabled', false);
                    //btnSubmit.find('i').removeClass('fa-spin fa-spinner').addClass('fa-save');
                    //alert( "complete" );
                    $('#preloader').addClass('d-none');
                });
              // return false;
            //});
            
            //alert(123);
        });
        
        
        $('body').off('click', '.btn-delete-quote-from-order').on('click', '.btn-delete-quote-from-order', function (e) {
            
            if(!confirm('Are you sure you want to delete this quote from order?')) {
                return '';
            }
            
            e.preventDefault();
            let menu = $(this);
            let productQuoteId = menu.data('product-quote-id');
            let orderId = menu.data('order-id');
            let url = menu.data('url');
            
            //alert(quoteId);
            //menu.find('.dropdown-menu').html('<a href="#" class="dropdown-item"><i class="fa fa-spin fa-spinner"></i> Loading ...</a>');
            $('#preloader').removeClass('d-none');
            
            $.ajax({
                  url: url,
                  type: 'post',
                  data: {'product_quote_id': productQuoteId, 'order_id': orderId},
                  dataType: 'json',
              })
                  .done(function(data) {
                      if (data.error) {
                          //alert(data.error);
                          new PNotify({
                                title: 'Error: delete quote from order',
                                type: 'error',
                                text: data.error,
                                hide: true
                            });
                      } else {
                          
                          $.pjax.reload({container: '#pjax-lead-orders', timout: 8000});
                          new PNotify({
                                title: 'Quote was successfully deleted',
                                type: 'success',
                                text: data.message,
                                hide: true
                            });
                      }
                  })
                .fail(function( jqXHR, textStatus ) {
                    alert( "Request failed: " + textStatus );
                }).always(function() {
                    //btnSubmit.prop('disabled', false);
                    //btnSubmit.find('i').removeClass('fa-spin fa-spinner').addClass('fa-save');
                    //alert( "complete" );
                    $('#preloader').addClass('d-none');
                });
              // return false;
            //});
            
            //alert(123);
        });
        
        
        
        $('body').off('click', '.btn-create-invoice').on('click', '.btn-create-invoice', function (e) {
            e.preventDefault();
            let url = $(this).data('url');
            //$('#preloader').removeClass('d-none');
            
            let modal = $('#modal-df');
            modal.find('.modal-body').html('');
            modal.find('.modal-title').html('Add Invoice');
            modal.find('.modal-body').load(url, function( response, status, xhr ) {
                //$('#preloader').addClass('d-none');
                if (status == 'error') {
                    alert(response);
                } else {
                    modal.modal({
                      backdrop: 'static',
                      show: true
                    });
                }
            });
        });
        
        $('body').off('click', '.btn-update-invoice').on('click', '.btn-update-invoice', function (e) {
            e.preventDefault();
            let url = $(this).data('url');
                    
            let modal = $('#modal-df');
            modal.find('.modal-body').html('');
            modal.find('.modal-title').html('Update Invoice');
            modal.find('.modal-body').load(url, function( response, status, xhr ) {
                //$('#preloader').addClass('d-none');
                if (status == 'error') {
                    alert(response);
                } else {
                    modal.modal({
                      backdrop: 'static',
                      show: true
                    });
                }
            });
        });
        
        
        $('body').off('click', '.btn-delete-invoice').on('click', '.btn-delete-invoice', function (e) {
            
            if(!confirm('Are you sure you want to delete this Invoice?')) {
                return '';
            }
            
            e.preventDefault();
            let menu = $(this);
            let invoiceId = menu.data('invoice-id');
            let orderId = menu.data('order-id');
            
            let url = menu.data('url');

            //menu.find('.dropdown-menu').html('<a href="#" class="dropdown-item"><i class="fa fa-spin fa-spinner"></i> Loading ...</a>');
            $('#preloader').removeClass('d-none');
            
            $.ajax({
                  url: url,
                  type: 'post',
                  data: {'id': invoiceId},
                  dataType: 'json',
              })
                  .done(function(data) {
                      if (data.error) {
                          //alert(data.error);
                          new PNotify({
                                title: 'Error: delete Invoice',
                                type: 'error',
                                text: data.error,
                                hide: true
                            });
                      } else {
                          
                          $.pjax.reload({container: '#pjax-order-invoice-' + orderId, timout: 8000});
                          new PNotify({
                                title: 'Invoice was successfully deleted',
                                type: 'success',
                                text: data.message,
                                hide: true
                            });
                      }
                  })
                .fail(function( jqXHR, textStatus ) {
                    alert( "Request failed: " + textStatus );
                }).always(function() {
                    //btnSubmit.prop('disabled', false);
                    //btnSubmit.find('i').removeClass('fa-spin fa-spinner').addClass('fa-save');
                    //alert( "complete" );
                    $('#preloader').addClass('d-none');
                });
        });
        
        
    });
    
JS;

$this->registerJs($js, \yii\web\View::POS_READY, 'update-hotel-request-js');

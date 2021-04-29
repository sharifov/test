<?php

/**
 * @var $this View
 * @var $dataProviderOrders \yii\data\ActiveDataProvider
 */

use yii\web\View;

?>
<style>
    .x_panel_orders {background-color: #cad7e4;}
</style>

<?php yii\widgets\Pjax::begin(['id' => 'pjax-case-orders', 'enablePushState' => false, 'timeout' => 10000]) ?>
    <div class="x_panel x_panel_orders">
        <div class="x_title">

            <h2><i class="fas fa-money-check-alt"></i> Orders (<?=$dataProviderOrders->totalCount?>)</h2>
            <ul class="nav navbar-right panel_toolbox">
                <!--
                <li>
                    <?php /* Html::a('<i class="fa fa-plus-circle success"></i> add Order', null, [
                        'data-url' => \yii\helpers\Url::to(['/order/order/create-ajax', 'id' => $lead->id]),
                        'class' => 'btn btn-light btn-create-order'
                    ]) */ ?>
                </li> -->
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content" style="display: block">

            <?= \yii\widgets\ListView::widget([
                'dataProvider' => $dataProviderOrders,

                /*'options' => [
                    'tag' => 'table',
                    'class' => 'table table-bordered',
                ],*/
                'emptyText' => '<div class="text-center">Not found orders</div>',
                //'layout' => "\n{items}<div class=\"text-center\">{pager}</div>\n", // {summary}\n<div class="text-center">{pager}</div>
                'itemView' => function ($model, $key, $index, $widget) {
                    return $this->render('_list_item', ['order' => $model, 'index' => $index]);
                },

                'itemOptions' => [
                    //'class' => 'item',
                    //'tag' => false,
                ],
            ]) ?>

        </div>
    </div>
<?php yii\widgets\Pjax::end() ?>


<?php

$js = <<<JS

    $('body').off('click', '.btn-complete-order').on('click', '.btn-complete-order', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        //$('#preloader').removeClass('d-none');
        
        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Complete Order');
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

    $('body').off('click', '.btn-cancel-order').on('click', '.btn-cancel-order', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        //$('#preloader').removeClass('d-none');
        
        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Cancel Order');
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

    $('body').off('click', '.btn-create-order').on('click', '.btn-create-order', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        //$('#preloader').removeClass('d-none');
        
        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Add order');
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
    
    $('body').off('click', '.btn-update-order').on('click', '.btn-update-order', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
                
        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Update order');
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
    
     $('body').off('click', '.btn-split').on('click', '.btn-split', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        let orderId = $(this).data('order-id');
        let title = $(this).data('title');
                
        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html(title);
        modal.find('.modal-body').load(url, {orderId: orderId}, function( response, status, xhr ) {
            //$('#preloader').addClass('d-none');
            if (status == 'error') {
                let message = xhr.status === 403 ? xhr.responseText : 'Internal Server Error.';
                new PNotify({
                    title: 'Error',
                    text: message,
                    type: 'error'
                 })
            } else {
                modal.modal({
                  show: true
                });
            }
        });
    });
    
    
    
    $('body').off('click', '.btn-delete-order').on('click', '.btn-delete-order', function(e) {
        
        if(!confirm('Are you sure you want to delete this order?')) {
            return '';
        }
        
      e.preventDefault();
      $('#preloader').removeClass('d-none');
      let orderId = $(this).data('order-id');
      let url = $(this).data('url');
           
      /*alert(productId);
      
      let btnSubmit = $(this).find(':submit');
      btnSubmit.prop('disabled', true);
      btnSubmit.find('i').removeClass('fa-save').addClass('fa-spin fa-spinner');*/

     // $('#preloader').removeClass('d-none');

      $.ajax({
          url: url,
          type: 'post',
          data: {'id': orderId},
          dataType: 'json',
      })
          .done(function(data) {
              if (data.error) {
                  alert(data.error);
                  new PNotify({
                        title: 'Error: delete order',
                        type: 'error',
                        text: data.error,
                        hide: true
                    });
              } else {
                  $.pjax.reload({container: '#pjax-case-orders', push: false, replace: false, async: false, timeout: 2000});
                  new PNotify({
                        title: 'The order was successfully removed',
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
    });
     
    $('body').off('click', '.btn-order-send-email-confirmation').on('click', '.btn-order-send-email-confirmation', function(e) {
      
        e.preventDefault();
        
        if(!confirm('Are you sure you want to send email confirmation?')) {
            return '';
        }
        
      let orderId = $(this).data('id');
      let url = $(this).data('url');
      
      $.ajax({
          url: url,
          type: 'post',
          data: {'id': orderId},
          dataType: 'json'
      })
          .done(function(data) {
              if (data.error) {
                  new PNotify({
                        title: 'Error: send email confirmation',
                        type: 'error',
                        text: data.message,
                        hide: true
                    });
              } else {
                  new PNotify({
                        title: 'The email was successfully sent',
                        type: 'success',
                        text: 'Success',
                        hide: true
                    });
              }
          })
        .fail(function( jqXHR, textStatus ) {
            alert( "Request failed: " + textStatus );
        }).always(function() {
        });
    });
    
    $('body').off('click', '.btn-order-generate-files').on('click', '.btn-order-generate-files', function(e) {
      
        e.preventDefault();        
        if(!confirm('Are you sure you want to generate file?')) {
            return false;
        }
        
      let orderId = $(this).data('id');
      let url = $(this).data('url');
      
      $.ajax({
          url: url,
          type: 'post',
          data: {'id': orderId},
          dataType: 'json'
      })
          .done(function(data) {
              if (data.error) {
                  new PNotify({
                        title: 'Error: file generating',
                        type: 'error',
                        text: data.message,
                        hide: true
                    });
              } else {
                  addFileToFileStorageList();        
                  new PNotify({
                        title: 'File generated',
                        type: 'success',
                        text: 'Success',
                        hide: true
                    });
              }
          })
        .fail(function( jqXHR, textStatus ) {
            alert( "Request failed: " + textStatus );
        }).always(function() {
        });
    });
    
    $('body').off('click', '.btn-cancel-process').on('click', '.btn-cancel-process', function(e) {
      
        e.preventDefault();
        
        if(!confirm('Are you sure you want to cancel this order process?')) {
            return '';
        }
        
      let orderId = $(this).data('order-id');
      let url = $(this).data('url');

      $.ajax({
          url: url,
          type: 'post',
          data: {'id': orderId},
          dataType: 'json',
      })
          .done(function(data) {
              if (data.error) {
                  new PNotify({
                        title: 'Error: cancel order process',
                        type: 'error',
                        text: data.message,
                        hide: true
                    });
              } else {
                  $.pjax.reload({container: '#pjax-case-orders', push: false, replace: false, async: false, timeout: 2000});
                  new PNotify({
                        title: 'The order process was successfully canceled',
                        type: 'success',
                        text: data.message,
                        hide: true
                    });
              }
          })
        .fail(function( jqXHR, textStatus ) {
            alert( "Request failed: " + textStatus );
        });
      // return false;
    });
    
    $('body').off('click', '.btn-start-process').on('click', '.btn-start-process', function(e) {
      
        e.preventDefault();
        
        if(!confirm('Are you sure you want to start this order process?')) {
            return '';
        }
        
      let orderId = $(this).data('order-id');
      let url = $(this).data('url');

      $.ajax({
          url: url,
          type: 'post',
          data: {'id': orderId},
          dataType: 'json',
      })
          .done(function(data) {
              if (data.error) {
                  new PNotify({
                        title: 'Error: start order process',
                        type: 'error',
                        text: data.message,
                        hide: true
                    });
              } else {
                  $.pjax.reload({container: '#pjax-case-orders', push: false, replace: false, async: false, timeout: 2000});
                  new PNotify({
                        title: 'The order process was successfully started',
                        type: 'success',
                        text: data.message,
                        hide: true
                    });
              }
          })
        .fail(function( jqXHR, textStatus ) {
            alert( "Request failed: " + textStatus );
        });
      // return false;
    });
    
    $(document).on('click', '.btn-order-status-log', function(e){        
        e.preventDefault();
        let url = $(this).data('url');
        let gid = $(this).data('gid');
        let modal = $('#modal-lg');
          
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Order [' + gid + '] status history');
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
    
    $(document).on('click', '.btn-invoice-status-log', function(e){        
        e.preventDefault();
        let url = $(this).data('url');
        let gid = $(this).data('gid');
        let modal = $('#modal-lg');
          
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Invoice [' + gid + '] status history');
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
    
    $(document).on('click', '.btn-payment-status-log', function(e){        
        e.preventDefault();
        let url = $(this).data('url');
        let modal = $('#modal-lg');
          
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Payment status log');
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
                  new PNotify({
                        title: 'Error: delete Invoice',
                        type: 'error',
                        text: data.error,
                        hide: true
                    });
              } else {
                  
                  pjaxReload({container: '#pjax-order-invoice-' + orderId, timout: 8000});
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
            $('#preloader').addClass('d-none');
        });
    });
    
    $('body').off('click', '.btn-payment-capture').on('click', '.btn-payment-capture', function (e) {
            
         e.preventDefault();
         
        if(!confirm('Are you sure you want to Capture this Payment?')) {
            return '';
        }

        let url = $(this).data('url');
        let paymentId = $(this).data('payment-id');
                    
        $.ajax({
              url: url,
              type: 'post',
              data: {id: paymentId},
              dataType: 'json',
          })
              .done(function(data) {
                  if (data.error) {
                      new PNotify({
                            title: 'Error: Capture Payment',
                            type: 'error',
                            text: data.message,
                            hide: true
                        });
                      return;
                  }
                  new PNotify({
                        title: 'Payment was successfully Capture',
                        type: 'success',
                        text: 'Success',
                        hide: true
                    });
                  pjaxReload({container: '#pjax-order-payment-' + paymentId, timout: 8000});
              })
            .fail(function( jqXHR, textStatus ) {
                alert( "Request failed: " + textStatus );
            }).always(function() {
                
            });
    });
    
    $('body').off('click', '.btn-payment-refund').on('click', '.btn-payment-refund', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        //$('#preloader').removeClass('d-none');
        
        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Payment Refund');
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
    
    $('body').off('click', '.btn-payment-update').on('click', '.btn-payment-update', function (e) {
        e.preventDefault();
        let url = $(this).data('url');
        
        let modal = $('#modal-df');
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Update Payment');
        modal.find('.modal-body').load(url, function( response, status, xhr ) {
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
    
    $('body').off('click', '.btn-payment-delete').on('click', '.btn-payment-delete', function (e) {
        
         e.preventDefault();
         
        if(!confirm('Are you sure you want to delete this Payment?')) {
            return '';
        }
        
       
        
        let url = $(this).data('url');
        let paymentId = $(this).data('payment-id');
        let orderId = $(this).data('order-id');
                    
        $.ajax({
              url: url,
              type: 'post',
              data: {id: paymentId},
              dataType: 'json',
          })
              .done(function(data) {
                  if (data.error) {
                      new PNotify({
                            title: 'Error: delete Payment',
                            type: 'error',
                            text: data.error,
                            hide: true
                        });
                      return;
                  }
                  pjaxReload({container: '#pjax-order-payment-' + orderId, timout: 8000, async: true});
                  new PNotify({
                        title: 'Payment was successfully deleted',
                        type: 'success',
                        text: data.message,
                        hide: true
                    });
                  if ($('#pjax-order-transaction-' + orderId).length) {
                      pjaxReload({container: '#pjax-order-transaction-' + orderId, async: true});
                  }
              })
            .fail(function( jqXHR, textStatus ) {
                alert( "Request failed: " + textStatus );
            }).always(function() {
                
            });
    });

    $('body').off('click', '.btn-payment-void').on('click', '.btn-payment-void', function (e) {
        
         e.preventDefault();
         
        if(!confirm('Are you sure you want to Void this Payment?')) {
            return '';
        }

        let url = $(this).data('url');
        let paymentId = $(this).data('payment-id');
                    
        $.ajax({
              url: url,
              type: 'post',
              data: {id: paymentId},
              dataType: 'json',
          })
              .done(function(data) {
                  if (data.error) {
                      new PNotify({
                            title: 'Error: Void Payment',
                            type: 'error',
                            text: data.message,
                            hide: true
                        });
                      return;
                  } 
                  new PNotify({
                        title: 'Payment was successfully Void',
                        type: 'success',
                        text: 'Success',
                        hide: true
                    });
                  pjaxReload({container: '#pjax-order-payment-' + paymentId, timout: 8000});
                 
              })
            .fail(function( jqXHR, textStatus ) {
                alert( "Request failed: " + textStatus );
            }).always(function() {
                
            });
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
                      new PNotify({
                            title: 'Error: delete quote from order',
                            type: 'error',
                            text: data.error,
                            hide: true
                        });
                  } else {
                      
                      // pjaxReload({container: '#pjax-lead-orders', timout: 2000});
                      pjaxReload({container: '#pjax-lead-orders'});
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
                $('#preloader').addClass('d-none');
            });
    });
JS;

$this->registerJs($js, View::POS_READY, 'case-order-js');


//$this->registerJs(
//
//        $("#pjax-lead-checklist").on("pjax:start", function () {
//            //$("#pjax-container").fadeOut("fast");
//            $("#btn-submit-checklist").attr("disabled", true).prop("disabled", true).addClass("disabled");
//            $("#btn-submit-checklist i").attr("class", "fa fa-spinner fa-pulse fa-fw")
//        });
//
//        $("#pjax-lead-checklist").on("pjax:end", function () {
//            //$("#pjax-container").fadeIn("fast");
//            $("#btn-submit-checklist").attr("disabled", false).prop("disabled", false).removeClass("disabled");
//            $("#btn-submit-checklist i").attr("class", "fa fa-plus");
//        });
//    '
//);
